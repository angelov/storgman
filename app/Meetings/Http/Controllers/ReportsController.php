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
use Angelov\Eestec\Platform\Meetings\Commands\CreateMeetingReportCommand;
use Angelov\Eestec\Platform\Meetings\MeetingsService;
use Angelov\Eestec\Platform\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Member;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class ReportsController extends BaseController
{
    protected $meetings;

    public function __construct(MeetingsRepositoryInterface $meetings) {
        $this->meetings = $meetings;
    }

    public function create($id)
    {
        $meeting = $this->meetings->get($id);

        return view('meetings.reports.create', compact('meeting'));
    }

    public function store($id, Request $request, MeetingsService $meetingsService, Guard $auth)
    {
        $minutes = $request->get('minutes', '');
        $attendants = $meetingsService->parseAttendantsIds($request->get('attendants'));

        /** @var Member $member*/
        $member = $auth->user();
        $reporterId = $member->getId();

        dispatch(new CreateMeetingReportCommand($id, $reporterId, $attendants, $minutes));

        session()->flash('action-message', 'Report stored successfully.');

        return redirect()->route('meetings.show', $id);
    }
}
