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

use Angelov\Eestec\Platform\Exceptions\ResourceNotFoundException;
use Angelov\Eestec\Platform\Factories\MembersFactory;
use Angelov\Eestec\Platform\Http\Requests\StoreMemberRequest;
use Angelov\Eestec\Platform\Http\Requests\UpdateMemberRequest;
use Angelov\Eestec\Platform\Paginators\MembersPaginator;
use Angelov\Eestec\Platform\Populators\MembersPopulator;
use Angelov\Eestec\Platform\Repositories\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repositories\PhotosRepositoryInterface;
use Angelov\Eestec\Platform\Services\MeetingsService;
use Angelov\Eestec\Platform\Services\MembershipService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
use Illuminate\Mail\Message;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class MembersController extends BaseController
{
    protected $request;
    protected $members;
    protected $paginator;
    protected $fees;
    protected $view;
    protected $authenticator;
    protected $session;
    protected $redirector;

    public function __construct(
        Request $request,
        Factory $view,
        Guard $authenticator,
        Store $session,
        Redirector $redirector,
        MembersRepositoryInterface $members,
        FeesRepositoryInterface $fees,
        MembersPaginator $paginator
    ) {
        $this->request = $request;
        $this->members = $members;
        $this->fees = $fees;
        $this->paginator = $paginator;
        $this->view = $view;
        $this->authenticator = $authenticator;
        $this->session = $session;
        $this->redirector = $redirector;
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

        return $this->view->make('members.index', compact('members', 'pending'));
    }

    /**
     * Returns the list of members to be used for autocompletion
     *
     * @param UrlGenerator $url
     * @return JsonResponse
     */
    public function prefetch(UrlGenerator $url)
    {
        $members = $this->members->all();
        $result = [];

        foreach ($members as $member) {
            $tmp = [];
            $tmp['value'] = $member->full_name;
            $tmp['image'] = $url->route('imagecache', ['xsmall', $member->photo]);
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
        return $this->view->make('members.board', compact('members'));
    }

    /**
     * Show a page with the unapproved member accounts
     *
     * @return Response
     */
    public function unapproved()
    {
        $members = $this->members->getUnapprovedMembers();
        return $this->view->make('members.unapproved', compact('members'));
    }

    /**
     * Show the form for creating a new member
     *
     * @return Response
     */
    public function create()
    {
        return $this->view->make('members.create');
    }

    /**
     * Store a newly created member in storage.
     *
     * @param StoreMemberRequest $request
     * @return Response
     */
    public function store(StoreMemberRequest $request)
    {
        $member = MembersFactory::createFromRequest($request);

        /**
         * Automatically approve the member's account when
         * it is created by the board members
         */
        $member->approved = true;

        $this->members->store($member);

        $this->session->flash('action-message', "Member added successfully.");

        return $this->redirector->route('members.index');
    }

    /**
     * Display the specified member.
     *
     * @param MembershipService $membershipService
     * @param \Angelov\Eestec\Platform\Services\MeetingsService $meetingsService
     * @param int $id
     * @return Response
     *
     * @todo Information separated in tabs (in the view) should be separated in few methods
     */
    public function show(MembershipService $membershipService, MeetingsService $meetingsService, $id)
    {
        $member = $this->members->get($id);

        $fees = $this->fees->getFeesForMember($member);

        /** @todo I don't like what i've done here. */
        $member->membership_status = $membershipService->isMemberActive($member);
        $member->membership_expiration_date = $membershipService->getExpirationDate($member);

        $attendance = $meetingsService->calculateAttendanceDetailsForMember($member);
        $joinedDate = $membershipService->getJoinedDate($member);

        $latestMeetings = $meetingsService->latestMeetingsAttendanceStatusForMember($member);

        $monthly = json_encode($meetingsService->calculateMonthlyAttendanceDetailsForMember($member));

        return $this->view->make('members.show', compact('member', 'attendance', 'fees',
                          'joinedDate', 'latestMeetings', 'monthly'));
    }

    /**
     * Returns html component with short member info
     * (focused on the membership)
     *
     * @param \Angelov\Eestec\Platform\Services\MembershipService $membershipService
     * @param int $id
     * @return Response
     */
    public function quickMemberInfo(MembershipService $membershipService, $id)
    {
        $member = $this->members->get($id);

        $member->membership_status = $membershipService->isMemberActive($member);

        $membershipStatus = $member->membership_status;
        $joinedDate = $membershipService->getJoinedDate($member)->toDateString();
        $expirationDate = $membershipService->getExpirationDate($member)->toDateString();

        return $this->view->make('members.components.quick-info',
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

        return $this->view->make('members.edit', compact('member'));
    }

    /**
     * Update the specified member in storage.
     *
     * @param MembersPopulator $populator
     * @param UpdateMemberRequest $request
     * @param  int $id
     * @return Response
     */
    public function update(MembersPopulator $populator, UpdateMemberRequest $request, $id)
    {
        $member = $this->members->get($id);

        $populator->populateFromRequest($member, $request);

        $this->members->store($member);

        $this->session->flash('action-message', "Member updated successfully.");

        return $this->redirector->route('members.index');
    }

    /**
     * Remove the specified members from storage.
     * Method available only via AJAX requests
     *
     * @param \Angelov\Eestec\Platform\Repositories\PhotosRepositoryInterface $photos
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy(PhotosRepositoryInterface $photos, $id)
    {
        $data = [];

        try {
            $member = $this->members->get($id);

            if (isset($member->photo)) {
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
     * @param Mailer $mailer
     * @param int $id
     * @return JsonResponse
     */
    public function approve(Mailer $mailer, $id)
    {
        /** @todo Duplicated code, create ResourceNotFound error handler  */

        try {
            $member = $this->members->get($id);
            $member->approved = true;
            $this->members->store($member);

            $mailer->send('emails.members.approved', compact('member'), function(Message $message) use ($member)
            {
                $message->to($member->email)->subject('Your account was approved!');
            });

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
     * @param Mailer $mailer
     * @param int $id
     * @return JsonResponse
     */
    public function decline(Mailer $mailer, $id)
    {
        /** @todo Duplicated code, create ResourceNotFound error handler  */

        try {
            $member = $this->members->get($id);

            $mailer->send('emails.members.declined', compact('member'), function(Message $message) use ($member)
            {
                $message->to($member->email)->subject('We are sorry...');
            });

            $this->members->destroy($id);

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
        return $this->view->make('members.register');
    }

    /**
     * Proceed the information submitted via the registration form
     *
     * @param StoreMemberRequest $request
     * @param Mailer $mailer
     * @return Response
     */
    public function postRegister(StoreMemberRequest $request, Mailer $mailer)
    {
        $member = MembersFactory::createFromRequest($request);

        $this->members->store($member);

        $mailer->send('emails.members.registered', compact('member'), function(Message $message) use ($member)
        {
            $message->to($member->email)->subject('Thank you for joining us!');
        });

        $this->session->flash('action-message',
            "Your account was created successfully. You will be notified when the board members approve it.");

        return $this->redirector->route('members.register');
    }
}
