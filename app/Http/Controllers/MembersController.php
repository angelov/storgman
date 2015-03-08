<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Http\Controllers;

use Angelov\Eestec\Platform\Commands\Members\ApproveMemberCommand;
use Angelov\Eestec\Platform\Commands\Members\CreateMemberCommand;
use Angelov\Eestec\Platform\Commands\Members\DeclineMemberCommand;
use Angelov\Eestec\Platform\Commands\Members\DeleteMemberCommand;
use Angelov\Eestec\Platform\Commands\Members\UpdateMemberCommand;
use Angelov\Eestec\Platform\Http\Requests\StoreMemberRequest;
use Angelov\Eestec\Platform\Http\Requests\UpdateMemberRequest;
use Angelov\Eestec\Platform\Paginators\MembersPaginator;
use Angelov\Eestec\Platform\Repositories\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Services\MeetingsService;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class MembersController extends BaseController
{
    protected $members;
    protected $paginator;
    protected $fees;
    protected $view;
    protected $authenticator;
    protected $session;
    protected $redirector;
    protected $commandBus;

    public function __construct(
        Factory $view,
        Store $session,
        Redirector $redirector,
        MembersRepositoryInterface $members,
        FeesRepositoryInterface $fees,
        Dispatcher $commandBus
    ) {
        $this->members = $members;
        $this->fees = $fees;
        $this->view = $view;
        $this->session = $session;
        $this->redirector = $redirector;
        $this->commandBus = $commandBus;
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

        return $this->view->make('members.board', compact('members'));
    }

    /**
     * Show a page with the unapproved member accounts
     *
     * @return View
     */
    public function unapproved()
    {
        $members = $this->members->getUnapprovedMembers();

        return $this->view->make('members.unapproved', compact('members'));
    }

    /**
     * Show the form for creating a new member
     *
     * @return View
     */
    public function create()
    {
        return $this->view->make('members.create');
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

        $this->commandBus->dispatch(new CreateMemberCommand($data, $approve = true));

        $this->session->flash('action-message', "Member added successfully.");

        return $this->redirector->route('members.index');
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

        return $this->view->make('members.show', compact('member', 'attendance', 'latestMeetings', 'monthly'));
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

        return $this->view->make('members.components.quick-info', compact('member'));
    }

    /**
     * Show the form for editing the specified member.
     *
     * @param  int      $id
     * @return View
     */
    public function edit($id)
    {
        $member = $this->members->get($id);

        return $this->view->make('members.edit', compact('member'));
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

        $this->commandBus->dispatch(new UpdateMemberCommand($id, $data));

        $this->session->flash('action-message', "Member updated successfully.");

        return $this->redirector->route('members.index');
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
        $this->commandBus->dispatch(new DeleteMemberCommand($id));

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
        $this->commandBus->dispatch(new ApproveMemberCommand($id));

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
        $this->commandBus->dispatch(new DeclineMemberCommand($id));

        return $this->successfulJsonResponse('Member declined successfully.');
    }

    /**
     * The new members can create their profiles on the system
     *
     * @return View
     */
    public function register()
    {
        return $this->view->make('members.register');
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

        $this->commandBus->dispatch(new CreateMemberCommand($data));

        $this->session->flash('action-message',
            "Your account was created successfully. You will be notified when the board members approve it.");

        return $this->redirector->route('members.register');
    }
}
