<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of EESTEC Platform.
 *
 * EESTEC Platform is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EESTEC Platform is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EESTEC Platform.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EESTEC Platform
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Http\Controllers;

use Angelov\Eestec\Platform\Exception\ResourceNotFoundException;
use Angelov\Eestec\Platform\Factory\MembersFactory;
use Angelov\Eestec\Platform\Paginator\MembersPaginator;
use Angelov\Eestec\Platform\Populator\MembersPopulator;
use Angelov\Eestec\Platform\Repository\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface;
use Angelov\Eestec\Platform\Service\MeetingsService;
use Angelov\Eestec\Platform\Service\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;
use Angelov\Eestec\Platform\Validation\MembersValidator;
use View;

class MembersController extends BaseController
{
    protected $request;
    protected $members;
    protected $validator;
    protected $paginator;
    protected $fees;

    public function __construct(
        Request $request,
        MembersRepositoryInterface $members,
        FeesRepositoryInterface $fees,
        MembersPaginator $paginator,
        MembersValidator $validator
    ) {
        $this->request = $request;
        $this->members = $members;
        $this->fees = $fees;
        $this->validator = $validator;
        $this->paginator = $paginator;
    }

    /**
     * Display a listing of members
     *
     * @return Response
     */
    public function index()
    {
        $page = $this->request->get('page', 1);
        $members = $this->paginator->get($page);

        /** @todo This can get a little optimized */
        $pending = count($this->members->getUnapprovedMembers());

        return View::make('members.index', compact('members', 'pending'));
    }

    /**
     * Returns the list of members to be used for autocompletion
     *
     * @return JsonResponse
     */
    public function prefetch()
    {
        $members = $this->members->all();
        $result = [];

        foreach ($members as $member) {
            $tmp = [];
            $tmp['value'] = $member->full_name;
            $tmp['image'] = URL::route('imagecache', ['xsmall', $member->photo]);
            $tmp['id'] = $member->id;

            $result[] = $tmp;
        }

        return new JsonResponse($result);
    }

    /**
     * Show a page that lists the board members
     *
     * @return Response
     */
    public function board()
    {
        $members = $this->members->getBoardMembers();
        return View::make('members.board', compact('members'));
    }

    /**
     * Show a page with the unapproved member accounts
     *
     * @return Response
     */
    public function unapproved()
    {
        $members = $this->members->getUnapprovedMembers();
        return View::make('members.unapproved', compact('members'));
    }

    /**
     * Show the form for creating a new member
     *
     * @return Response
     */
    public function create()
    {
        return View::make('members.create');
    }

    /**
     * Store a newly created member in storage.
     *
     * @return Response
     */
    public function store()
    {
        if (!$this->validator->validate($this->request->all())) {
            $errorMessages = $this->validator->getMessages();
            Session::flash('errorMessages', $errorMessages);

            return Redirect::back()->withInput();
        }

        $member = MembersFactory::createFromRequest($this->request);

        /**
         * Automatically approve the member's account when
         * it is created by the board members
         */
        $member->approved = true;

        $this->members->store($member);

        Session::flash('action-message', "Member added successfully.");

        return Redirect::route('members.index');
    }

    /**
     * Display the specified member.
     *
     * @param  int      $id
     * @return Response
     *
     * @todo Information separated in tabs (in the view) should be separated in few methods
     */
    public function show($id)
    {
        $member = $this->members->get($id);

        /** Method dependency injection is coming soon \m/ */

        /** @var MembershipService $membershipService */
        $membershipService = App::make('MembershipService');

        /** @var MeetingsService $meetingsService */
        $meetingsService = App::make('MeetingsService');

        $fees = $this->fees->getFeesForMember($member);

        /** @todo I don't like what i've done here. */
        $member->membership_status = $membershipService->isMemberActive($member);
        $member->membership_expiration_date = $membershipService->getExpirationDate($member);

        $attendance = $meetingsService->calculateAttendanceDetailsForMember($member);
        $joinedDate = $membershipService->getJoinedDate($member);

        $latestMeetings = $meetingsService->latestMeetingsAttendanceStatusForMember($member);

        $monthly = json_encode($meetingsService->calculateMonthlyAttendanceDetailsForMember($member));

        return View::make('members.show', compact('member', 'attendance', 'fees',
                          'joinedDate', 'latestMeetings', 'monthly'));
    }

    /**
     * Returns html component with short member info
     * (focused on the membership)
     *
     * @param int $id
     * @return Response
     */
    public function quickMemberInfo($id)
    {
        $member = $this->members->get($id);

        /** @var MembershipService $membershipService */
        $membershipService = App::make('MembershipService');
        $member->membership_status = $membershipService->isMemberActive($member);

        $membershipStatus = $member->membership_status;
        $joinedDate = $membershipService->getJoinedDate($member)->toDateString();
        $expirationDate = $membershipService->getExpirationDate($member)->toDateString();

        return View::make('members.components.quick-info',
            compact('member', 'membershipStatus', 'joinedDate', 'expirationDate'));
    }

    /**
     * Show the form for editing the specified member.
     *
     * @param  int      $id
     * @return Response
     */
    public function edit($id)
    {
        $member = $this->members->get($id);

        return View::make('members.edit', compact('member'));
    }

    /**
     * Update the specified member in storage.
     *
     * @param  int      $id
     * @return Response
     */
    public function update($id)
    {
        $member = $this->members->get($id);

        /**
         * If the unique email rule is set and the member's email
         * is not changed, the system will consider the email as
         * already taken and will throw an error.
         *
         * @todo There is probably better way to do this
         */
        if ($member->email == $this->request->get('email')) {
            $this->validator->removeRule('email', 'unique');
        }

        /**
         * We don't want to change the member's password if there's
         * no new password inserted.
         *
         * @todo There is probably better way to do this
         */
        if ($this->request->get('password') == '') {
            $this->validator->removeRule('password', 'required');
            $this->validator->removeRule('password', 'min');
        }

        if (!$this->validator->validate($this->request->all())) {
            $errorMessages = $this->validator->getMessages();
            Session::flash('errorMessages', $errorMessages);

            return Redirect::back()->withInput();
        }

        /** @var MembersPopulator $filler */
        $populator = App::make('MembersPopulator');
        $populator->populateFromRequest($member, $this->request);

        $this->members->store($member);

        Session::flash('action-message', "Member updated successfully.");

        return Redirect::route('members.index');
    }

    /**
     * Remove the specified members from storage.
     * Method available only via AJAX requests
     *
     * @param  int      $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $data = [];

        try {
            $member = $this->members->get($id);

            if (isset($member->photo)) {
                /** @var PhotosRepositoryInterface $photos */
                $photos = App::make('PhotosRepository');

                $photos->destroy($member->photo, 'members');
            }

            $this->members->destroy($id);

            $data['status'] = 'success';
            $data['message'] = 'Member deleted successfully.';
        } catch (ResourceNotFoundException $e) {
            $data['status'] = 'warning';
            $data['message'] = 'There was something wrong with your request.';
        }

        return new JsonResponse($data);
    }

    /**
     * Approve a pending member account
     * Method available only via AJAX requests
     *
     * @param int   $id
     * @return JsonResponse
     */
    public function approve($id)
    {
        /** @todo Duplicated code, create ResourceNotFound error handler  */

        try {
            $member = $this->members->get($id);
            $member->approved = true;
            $this->members->store($member);

            $data['status'] = 'success';
            $data['message'] = 'Member approved successfully.';
        } catch (ResourceNotFoundException $e) {
            $data['status'] = 'warning';
            $data['message'] = 'There was something wrong with your request.';
        }

        return new JsonResponse($data);
    }

    /**
     * Decline a pending member account
     * Method available only via AJAX requests
     *
     * @param int   $id
     * @return JsonResponse
     */
    public function decline($id)
    {
        /** @todo Duplicated code, create ResourceNotFound error handler  */

        try {
            $this->members->destroy($id);

            /** @todo Send an email saying that the account was declined */

            $data['status'] = 'success';
            $data['message'] = 'Member declined successfully.';
        } catch (ResourceNotFoundException $e) {
            $data['status'] = 'warning';
            $data['message'] = 'There was something wrong with your request.';
        }

        return new JsonResponse($data);
    }

    /**
     * The new members can create their profiles on the system
     *
     * @return Response
     */
    public function register()
    {
        return View::make('members.register');
    }

    /**
     * Proceed the information submitted via the registration form
     *
     * @return Response
     */
    public function postRegister()
    {
        /** @todo Duplicated code */
        if (!$this->validator->validate($this->request->all())) {
            $errorMessages = $this->validator->getMessages();
            Session::flash('errorMessages', $errorMessages);

            return Redirect::back()->withInput();
        }

        $member = MembersFactory::createFromRequest($this->request);

        $this->members->store($member);

        Session::flash('action-message',
            "Your account was created successfully. You will be notified when the board members approve it.");

        return Redirect::route('members.register');
    }
}
