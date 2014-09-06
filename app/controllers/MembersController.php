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

use Angelov\Eestec\Platform\Exception\ResourceNotFoundException;
use Angelov\Eestec\Platform\Factory\MembersFactory;
use Angelov\Eestec\Platform\Populator\MembersPopulator;
use Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface;
use Angelov\Eestec\Platform\Service\MeetingsService;
use Angelov\Eestec\Platform\Service\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;
use Angelov\Eestec\Platform\Validation\MembersValidator;

class MembersController extends \BaseController
{

    protected $request;
    protected $members;
    protected $validator;

    public function __construct(
        Request $request,
        MembersRepositoryInterface $members,
        MembersValidator $validator
    ) {
        $this->request = $request;
        $this->members = $members;
        $this->validator = $validator;
    }

    /**
     * Display a listing of members
     *
     * @return Response
     */
    public function index()
    {
        $page = $this->request->get('page', 1);
        $perPage = 15;
        $data = $this->members->getByPage($page, $perPage);

        $members = Paginator::make($data->items, $data->totalItems, $perPage);
        $count = $this->members->countAll();

        return View::make('members.index', compact('members', 'count'));
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

        $this->members->store($member);

        Session::flash('action-message', "Member added successfully.");

        return Redirect::route('members.index');
    }

    /**
     * Display the specified member.
     *
     * @param  int      $id
     * @return Response
     */
    public function show($id)
    {
        $member = $this->members->get($id);

        /** @var MembershipService $membershipService */
        $membershipService = App::make('MembershipService');

        /** @var MeetingsService $meetingsService */
        $meetingsService = App::make('MeetingsService');

        $member->membership_status = $membershipService->isMemberActive($member);
        $member->membership_expiration_date = $membershipService->getExpirationDate($member);

        $attendanceRate = $meetingsService->calculateAttendanceRateForMember($member);

        return View::make('members.show', compact('member', 'attendanceRate'));
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

}
