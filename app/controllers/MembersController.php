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

use Angelov\Eestec\Platform\Service\MeetingsService;
use Angelov\Eestec\Platform\Service\MembershipService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Angelov\Eestec\Platform\Exception\MemberNotFoundException;
use Angelov\Eestec\Platform\Model\Member;
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

        $this->beforeFilter('auth');
        $this->beforeFilter('boardMember');
    }

    /**
     * Display a listing of members
     *
     * @return Response
     */
    public function index()
    {

        // load the members for autocompletion
        if ($this->request->ajax()) {
            $members = $this->members->all();
            $result = [];

            foreach ($members as $member) {
                $tmp['value'] = $member->full_name;
                $tmp['image'] = URL::route('imagecache', ['xsmall', $member->photo]);
                $tmp['id'] = $member->id;

                $result[] = $tmp;
            }

            return json_encode($result);
        }

        $page = $this->request->get('page', 1);
        $perPage = 15;
        $data = $this->members->getByPage($page, $perPage);

        $members = Paginator::make($data->items, $data->totalItems, $perPage);

        return View::make('members.index', compact('members'));
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

        $member = new Member();

        $member->first_name = $this->request->get('first_name');
        $member->last_name = $this->request->get('last_name');
        $member->birthday = $this->request->get('birthday');
        $member->email = $this->request->get('email');
        $member->password = Hash::make('123456'); // Hash::make(str_random(8))
        $member->faculty = $this->request->get('faculty');
        $member->field_of_study = $this->request->get('field_of_study');
        $member->board_member = ($this->request->get('board_member') == 1);
        $member->position_title = $this->request->get('position_title');

        if ($this->request->hasFile('member_photo')) {

            $photo = $this->request->file('member_photo');

            /** @var \Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface $photos */
            $photos = App::make('PhotosRepository');

            $photoFileName = md5($member->email) . "." . $photo->getClientOriginalExtension();
            $photos->store($photo, 'members', $photoFileName);

            $member->photo = $photoFileName;

        }

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

        $member->first_name = $this->request->get('first_name');
        $member->last_name = $this->request->get('last_name');
        $member->birthday = $this->request->get('birthday');
        //$member->password = Hash::make('123456'); // Hash::make(str_random(8))
        $member->faculty = $this->request->get('faculty');
        $member->field_of_study = $this->request->get('field_of_study');
        $member->board_member = ($this->request->get('board_member') == 1);
        $member->position_title = $this->request->get('position_title');
        $member->email = $this->request->get('email');


        if ($this->request->hasFile('member_photo')) {

            $photo = $this->request->file('member_photo');

            /** @var \Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface $photos */
            $photos = App::make('PhotosRepository');

            $photoFileName = md5($member->email) . "." . $photo->getClientOriginalExtension();
            $photos->store($photo, 'members', $photoFileName);

            $member->photo = $photoFileName;

        }

        $this->members->store($member);

        Session::flash('action-message', "Member updated successfully.");

        return Redirect::route('members.index');
    }

    /**
     * Remove the specified members from storage.
     * Method available only via AJAX requests
     *
     * @param  int      $id
     * @return Response
     */
    public function destroy($id)
    {

        if (!$this->request->ajax()) {
            return new Response();
        }

        $data = [];

        try {
            $member = $this->members->get($id);

            if (isset($member->photo)) {
                /** @var \Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface $photos */
                $photos = App::make('PhotosRepository');

                $photos->destroy($member->photo, 'members');
            }

            $this->members->destroy($id);

            $data['status'] = 'success';
            $data['message'] = 'Member deleted successfully.';
        } catch (MemberNotFoundException $e) {
            $data['status'] = 'warning';
            $data['message'] = 'There was something wrong with your request.';
        }

        return new JsonResponse($data);

    }

}
