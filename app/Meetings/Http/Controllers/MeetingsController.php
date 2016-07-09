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

namespace Angelov\Eestec\Platform\Meetings\Http\Controllers;

use Angelov\Eestec\Platform\Core\Http\Controllers\BaseController;
use Angelov\Eestec\Platform\Meetings\Attachments\FileSize;
use Angelov\Eestec\Platform\Meetings\Commands\CreateMeetingCommand;
use Angelov\Eestec\Platform\Meetings\Commands\DeleteMeetingCommand;
use Angelov\Eestec\Platform\Meetings\Commands\UpdateMeetingCommand;
use Angelov\Eestec\Platform\Meetings\Commands\UpdateMeetingReportCommand;
use Angelov\Eestec\Platform\Meetings\Http\Requests\StoreMeetingRequest;
use Angelov\Eestec\Platform\Meetings\MeetingsPaginator;
use Angelov\Eestec\Platform\Meetings\MeetingsService;
use Angelov\Eestec\Platform\Members\Member;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Angelov\Eestec\Platform\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class MeetingsController extends BaseController
{
    protected $request;
    protected $meetings;
    protected $members;
    protected $paginator;
    protected $meetingsService;
    protected $view;
    protected $authenticator;
    protected $session;
    protected $redirector;
    protected $commandBus;

    public function __construct(
        Request $request,
        Factory $view,
        Guard $authenticator,
        Store $session,
        Redirector $redirector,
        MeetingsRepositoryInterface $meetings,
        MembersRepositoryInterface $members,
        MeetingsPaginator $paginator,
        MeetingsService $meetingsService,
        Dispatcher $commandBus
    ) {
        $this->request = $request;
        $this->meetings = $meetings;
        $this->members = $members;
        $this->paginator = $paginator;
        $this->meetingsService = $meetingsService;
        $this->view = $view;
        $this->authenticator = $authenticator;
        $this->session = $session;
        $this->redirector = $redirector;
        $this->commandBus = $commandBus;
    }

    /**
     * Display a listing of the meetings.
     * GET /meetings
     *
     * @return View
     */
    public function index()
    {
        $page = $this->request->get('page', 1);
        $meetings = $this->paginator->get($page, ['attendants']);

        return $this->view->make('meetings.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new meeting report.
     * GET /meetings/create
     *
     * @return View
     */
    public function create()
    {
        return $this->view->make('meetings.create');
    }

    /**
     * Store a newly created meeting report in storage.
     * POST /meetings
     *
     * @param StoreMeetingRequest $request
     * @return RedirectResponse
     */
    public function store(StoreMeetingRequest $request)
    {
        $title = $request->get('title', '');
        $location = $request->get('location');
        $date = $request->get('date') ." ". $request->get('time');
        $details = $request->get('details', '');
        $notifyMembers = $request->get('notify') == '1' ? true : false;
        $attachments = $this->parseAttachmentIds($request->get('attachments'));

        /** @var Member $author */
        $author = $this->authenticator->user();
        $authorId = $author->getId();

        $command = new CreateMeetingCommand($title, $location, $date, $details, $authorId, $attachments, $notifyMembers);

        $meeting = $this->commandBus->dispatch($command);

        $this->session->flash('action-message', 'Meeting added successfully.');

        return $this->redirector->route('meetings.show', $meeting->getId());
    }

    // @todo move to separate class or something
    private function parseAttachmentIds($attachments)
    {
        $attachments = json_decode($attachments, true);

        $attachments = array_map(function($id) {
            return (int) $id;
        }, $attachments);

        return $attachments;
    }

    /**
     * Display the specified resource.
     * GET /meetings/{id}
     *
     * @param  int      $id
     * @return View
     */
    public function show($id)
    {
        $meeting = $this->meetings->get($id);

        return $this->view->make('meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified resource.
     * GET /meetings/{id}/edit
     *
     * @param  int      $id
     * @return View
     */
    public function edit($id)
    {
        $meeting = $this->meetings->get($id);
        $attendants = $meeting->getAttendants();
        $attendantsIds = $this->meetingsService->prepareAttendantsIds($attendants);

        return $this->view->make('meetings.edit', compact('meeting', 'attendantsIds'));
    }

    /**
     * Update the specified resource in storage.
     * PUT /meetings/{id}
     *
     * @param StoreMeetingRequest $request
     * @param  int $id
     * @return RedirectResponse
     */
    public function update(StoreMeetingRequest $request, $id)
    {
        $title = $request->get('title');
        $date = $request->get('date') ." ". $request->get('time');
        $details = $request->get('details', '');
        $location = $request->get('location');
        $attachments = $this->parseAttachmentIds($request->get('attachments'));

        $this->commandBus->dispatch(new UpdateMeetingCommand($id, $title, $location, $date, $details, $attachments));

        $minutes = $request->get('minutes', '');
        $attendants = $this->meetingsService->parseAttendantsIds($request->get('attendants', ''));

        if (count($attendants) > 0) { // maybe we should get the Meeting object and check there?
            $this->commandBus->dispatch(new UpdateMeetingReportCommand($id, $attendants, $minutes));
        }

        $this->session->flash('action-message', 'Meeting updated successfully.');

        return $this->redirector->route('meetings.index');
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /meetings/{id}
     *
     * @param  int      $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $this->commandBus->dispatch(new DeleteMeetingCommand($id));

        return $this->successfulJsonResponse('Meeting deleted successfully.');
    }
}
