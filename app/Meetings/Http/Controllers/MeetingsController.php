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

namespace Angelov\Storgman\Meetings\Http\Controllers;

use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Meetings\Commands\CreateMeetingCommand;
use Angelov\Storgman\Meetings\Commands\DeleteMeetingCommand;
use Angelov\Storgman\Meetings\Commands\UpdateMeetingCommand;
use Angelov\Storgman\Meetings\Commands\UpdateMeetingReportCommand;
use Angelov\Storgman\Meetings\Exceptions\NoPreviousMeetingException;
use Angelov\Storgman\Meetings\Http\Requests\StoreMeetingRequest;
use Angelov\Storgman\Meetings\MeetingsPaginator;
use Angelov\Storgman\Meetings\MeetingsService;
use Angelov\Storgman\Members\Member;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Angelov\Storgman\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;

class MeetingsController extends BaseController
{
    protected $meetings;
    protected $members;
    protected $meetingsService;

    public function __construct(MeetingsRepositoryInterface $meetings, MembersRepositoryInterface $members, MeetingsService $meetingsService)
    {
        $this->meetings = $meetings;
        $this->members = $members;
        $this->meetingsService = $meetingsService;
    }

    /**
     * Display a listing of the meetings.
     * GET /meetings
     *
     * @param Request $request
     * @param MeetingsPaginator $paginator
     * @return View
     */
    public function index(Request $request, MeetingsPaginator $paginator)
    {
        $page = $request->get('page', 1);
        $meetings = $paginator->get($page, ['attendants']);

        return view('meetings.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new meeting report.
     * GET /meetings/create
     *
     * @return View
     */
    public function create()
    {
        return view('meetings.create');
    }

    /**
     * Store a newly created meeting report in storage.
     * POST /meetings
     *
     * @param StoreMeetingRequest $request
     * @param Guard $auth
     * @return RedirectResponse
     */
    public function store(StoreMeetingRequest $request, Guard $auth)
    {
        $title = $request->get('title', '');
        $location = $request->get('location');
        $date = $request->get('date') ." ". $request->get('time');
        $details = $request->get('details', '');
        $notifyMembers = $request->get('notify') == '1' ? true : false;
        $attachments = $this->parseAttachmentIds($request->get('attachments'));

        /** @var Member $author */
        $author = $auth->user();
        $authorId = $author->getId();

        $command = new CreateMeetingCommand($title, $location, $date, $details, $authorId, $attachments, $notifyMembers);

        $meeting = dispatch($command);

        session()->flash('action-message', 'Meeting added successfully.');

        return redirect()->route('meetings.show', $meeting->getId());
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

        $averageAttendants = $this->meetings->getAverageNumberOfAttendants();
        $previousMeeting = null;

        try {
            $previousMeeting = $this->meetings->getPreviousMeeting($meeting);
        } catch (NoPreviousMeetingException $e) {
            $previousMeeting = null;
        }

        $attendantsType = json_encode($this->meetings->getAttendantsTypeForMeeting($meeting));

        return view('meetings.show', compact('meeting', 'averageAttendants', 'previousMeeting', 'attendantsType'));
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

        return view('meetings.edit', compact('meeting', 'attendantsIds'));
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

        dispatch(new UpdateMeetingCommand($id, $title, $location, $date, $details, $attachments));

        $minutes = $request->get('minutes', '');
        $attendants = $this->meetingsService->parseAttendantsIds($request->get('attendants', ''));

        if (count($attendants) > 0) { // maybe we should get the Meeting object and check there?
            dispatch(new UpdateMeetingReportCommand($id, $attendants, $minutes));
        }

        session()->flash('action-message', 'Meeting updated successfully.');

        return redirect()->route('meetings.index');
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
        dispatch(new DeleteMeetingCommand($id));

        return $this->successfulJsonResponse('Meeting deleted successfully.');
    }
}
