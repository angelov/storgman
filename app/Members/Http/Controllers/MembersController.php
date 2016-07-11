<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Members\Http\Controllers;

use Angelov\Eestec\Platform\Core\Http\Controllers\BaseController;
use Angelov\Eestec\Platform\Faculties\Repositories\FacultiesRepositoryInterface;
use Angelov\Eestec\Platform\Members\Commands\ApproveMemberCommand;
use Angelov\Eestec\Platform\Members\Commands\CreateMemberCommand;
use Angelov\Eestec\Platform\Members\Commands\DeclineMemberCommand;
use Angelov\Eestec\Platform\Members\Commands\DeleteMemberCommand;
use Angelov\Eestec\Platform\Members\Commands\UpdateMemberCommand;
use Angelov\Eestec\Platform\Members\Http\Requests\StoreMemberRequest;
use Angelov\Eestec\Platform\Members\Http\Requests\UpdateMemberRequest;
use Angelov\Eestec\Platform\Members\MembersPaginator;
use Angelov\Eestec\Platform\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Meetings\MeetingsService;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MembersController extends BaseController
{
    protected $members;
    protected $fees;

    public function __construct(MembersRepositoryInterface $members, FeesRepositoryInterface $fees)
    {
        $this->members = $members;
        $this->fees = $fees;
    }

    /**
     * Display a listing of members
     *
     * @param Request $request
     * @param MembersPaginator $paginator
     * @return View
     */
    public function index(Request $request, MembersPaginator $paginator)
    {
        $page = $request->get('page', 1);
        $members = $paginator->get($page);

        /** @todo This can get a little optimized */
        $pending = count($this->members->getUnapprovedMembers());

        return view('members.index', compact('members', 'pending'));
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
            $tmp['value'] = $member->getFullName();
            $tmp['image'] = $url->route('imagecache', ['xsmall', $member->getPhoto()]);
            $tmp['id'] = $member->getId();

            $result[] = $tmp;
        }

        return new JsonResponse($result);
    }

    /**
     * Show a page that lists the board members
     *
     * @return View
     */
    public function board()
    {
        $members = $this->members->getBoardMembers();

        return view('members.board', compact('members'));
    }

    /**
     * Show a page with the unapproved member accounts
     *
     * @return View
     */
    public function unapproved()
    {
        $members = $this->members->getUnapprovedMembers();

        return view('members.unapproved', compact('members'));
    }

    /**
     * Show the form for creating a new member
     *
     * @param FacultiesRepositoryInterface $faculties
     * @return View
     */
    public function create(FacultiesRepositoryInterface $faculties)
    {
        $faculties = $faculties->getEnabled();

        return view('members.create', compact('faculties'));
    }

    /**
     * Store a newly created member in storage.
     *
     * @param StoreMemberRequest $request
     * @return RedirectResponse
     */
    public function store(StoreMemberRequest $request)
    {
        $data = $request->all();

        dispatch(new CreateMemberCommand($data, $approve = true));

        session()->flash('action-message', "Member added successfully.");

        return redirect()->route('members.index');
    }

    /**
     * Display the specified member.
     *
     * @param MeetingsService $meetingsService
     * @param int $id
     * @return View
     *
     * @todo Information separated in tabs (in the view) should be separated in few methods
     */
    public function show(MeetingsService $meetingsService, $id)
    {
        $member = $this->members->get($id);

        $attendance = $meetingsService->calculateAttendanceDetailsForMember($member);

        $latestMeetings = $meetingsService->latestMeetingsAttendanceStatusForMember($member);

        $monthly = json_encode($meetingsService->calculateMonthlyAttendanceDetailsForMember($member));

        return view('members.show', compact('member', 'attendance', 'latestMeetings', 'monthly'));
    }

    /**
     * Returns html component with short member info
     * (focused on the membership)
     *
     * @param int $id
     * @return View
     */
    public function quickMemberInfo($id)
    {
        $member = $this->members->get($id);

        return view('members.components.quick-info', compact('member'));
    }

    /**
     * Show the form for editing the specified member.
     *
     * @param  int $id
     * @param FacultiesRepositoryInterface $faculties
     * @return View
     */
    public function edit($id, FacultiesRepositoryInterface $faculties)
    {
        $member = $this->members->get($id);
        $faculties = $faculties->getEnabled();

        return view('members.edit', compact('member', 'faculties'));
    }

    /**
     * Update the specified member in storage.
     *
     * @param UpdateMemberRequest $request
     * @param  int $id
     * @return RedirectResponse
     */
    public function update(UpdateMemberRequest $request, $id)
    {
        $data = $request->all();

        dispatch(new UpdateMemberCommand($id, $data));

        session()->flash('action-message', "Member updated successfully.");

        return redirect()->route('members.index');
    }

    /**
     * Remove the specified members from storage.
     * Method available only via AJAX requests
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        dispatch(new DeleteMemberCommand($id));

        return $this->successfulJsonResponse('Member deleted successfully.');
    }

    /**
     * Approve a pending member account
     * Method available only via AJAX requests
     *
     * @param int $id
     * @return JsonResponse
     */
    public function approve($id)
    {
        dispatch(new ApproveMemberCommand($id));

        return $this->successfulJsonResponse('Member approved successfully.');
    }

    /**
     * Decline a pending member account
     * Method available only via AJAX requests
     *
     * @param int $id
     * @return JsonResponse
     */
    public function decline($id)
    {
        dispatch(new DeclineMemberCommand($id));

        return $this->successfulJsonResponse('Member declined successfully.');
    }

    /**
     * The new members can create their profiles on the system
     *
     * @param FacultiesRepositoryInterface $faculties
     * @return View
     */
    public function register(FacultiesRepositoryInterface $faculties)
    {
        $faculties = $faculties->getEnabled();

        return view('members.register', compact('faculties'));
    }

    /**
     * Proceed the information submitted via the registration form
     *
     * @param StoreMemberRequest $request
     * @return RedirectResponse
     */
    public function postRegister(StoreMemberRequest $request)
    {
        $data = $request->all();

        dispatch(new CreateMemberCommand($data));

        session()->flash(
            'action-message',
            "Your account was created successfully. You will be notified when the board members approve it."
        );

        return redirect()->route('members.register');
    }
}
