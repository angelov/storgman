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

namespace Angelov\Eestec\Platform\Meetings\Handlers;

use Angelov\Eestec\Platform\Meetings\Commands\CreateMeetingCommand;
use Angelov\Eestec\Platform\Meetings\Meeting;
use Angelov\Eestec\Platform\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;

class CreateMeetingCommandHandler
{
    protected $meetings;
    protected $members;

    public function __construct(MeetingsRepositoryInterface $meetings, MembersRepositoryInterface $members)
    {
        $this->meetings = $meetings;
        $this->members = $members;
    }

    public function handle(CreateMeetingCommand $command)
    {
        $meeting = new Meeting();

        if (($title = $command->getTitle()) != "") {
            $meeting->setTitle($title);
        }

        $meeting->setDate(new \DateTime($command->getDate()));
        $meeting->setLocation($command->getLocation());

        $creator = $this->members->get($command->getCreatorId());
        $meeting->setCreator($creator);

        $meeting->setInfo($command->getDetails());

        $this->meetings->store($meeting);

        // @todo fire an event
    }
}
