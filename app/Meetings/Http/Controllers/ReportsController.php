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
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class ReportsController extends BaseController
{
    protected $view;
    protected $meetings;
    protected $commandBus;
    protected $session;
    protected $redirector;

    public function __construct(
        MeetingsRepositoryInterface $meetings,
        Factory $view,
        Dispatcher $commandBus,
        Store $session,
        Redirector $redirector
    ) {
        $this->view = $view;
        $this->meetings = $meetings;
        $this->commandBus = $commandBus;
        $this->session = $session;
        $this->redirector = $redirector;
    }

    public function create($id)
    {
        $meeting = $this->meetings->get($id);

        return $this->view->make('meetings.create-report', compact('meeting'));
    }

    public function store($id, Request $request, MeetingsService $meetingsService, Guard $auth)
    {
        $minutes = $request->get('minutes', '');
        $attendants = $meetingsService->parseAttendantsIds($request->get('attendants'));
        $reporterId = $auth->user()->getId();

        $this->commandBus->dispatch(new CreateMeetingReportCommand($id, $reporterId, $attendants, $minutes));

        $this->session->flash('action-message', 'Report stored successfully.');

        return $this->redirector->route('meetings.show', $id);
    }
}