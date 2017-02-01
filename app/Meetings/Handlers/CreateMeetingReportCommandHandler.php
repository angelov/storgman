<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of Storgman.
 *
 * Storgman is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Storgman is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Storgman.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Storgman
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Meetings\Handlers;

use Angelov\Storgman\Meetings\Commands\CreateMeetingReportCommand;
use Angelov\Storgman\Meetings\Events\MeetingReportWasCreatedEvent;
use Angelov\Storgman\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class CreateMeetingReportCommandHandler
{
    protected $meetings;
    protected $members;
    protected $events;

    public function __construct(MeetingsRepositoryInterface $meetings, MembersRepositoryInterface $members, Dispatcher $events)
    {
        $this->meetings = $meetings;
        $this->members = $members;
        $this->events = $events;
    }

    public function handle(CreateMeetingReportCommand $command)
    {
        $meeting = $this->meetings->get($command->getMeetingId());

        $reporter = $this->members->get($command->getReporterId());
        $meeting->setReportAuthor($reporter);

        $minutes = $command->getMinutes();
        $meeting->setMinutes($minutes);

        $attendants = $this->members->getByIds($command->getAttendants());
        $meeting->addAttendants($attendants);

        $this->meetings->store($meeting);

        $this->events->fire(new MeetingReportWasCreatedEvent($meeting));
    }
}