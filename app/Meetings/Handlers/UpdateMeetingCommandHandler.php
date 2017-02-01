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

namespace Angelov\Storgman\Meetings\Handlers;

use Angelov\Storgman\Meetings\Attachments\Attachment;
use Angelov\Storgman\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Storgman\Meetings\Commands\UpdateMeetingCommand;
use Angelov\Storgman\Meetings\Events\MeetingWasUpdatedEvent;
use Angelov\Storgman\Meetings\Meeting;
use Angelov\Storgman\Meetings\Repositories\MeetingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateMeetingCommandHandler
{
    protected $meetings;
    protected $attachments;
    protected $events;

    public function __construct(MeetingsRepositoryInterface $meetings, AttachmentsRepositoryInterface $attachments, Dispatcher $events)
    {
        $this->meetings = $meetings;
        $this->attachments = $attachments;
        $this->events = $events;
    }

    public function handle(UpdateMeetingCommand $command)
    {
        $meeting = $this->meetings->get($command->getMeetingId());

        $meeting->setTitle($command->getTitle());
        $meeting->setDate(new \DateTime($command->getDate()));
        $meeting->setLocation($command->getLocation());
        $meeting->setInfo($command->getDetails());

        $attachments = $this->attachments->getByIds($command->getAttachments());

        $this->syncAttachments($meeting, $attachments);

        $this->meetings->store($meeting);

        $this->events->fire(new MeetingWasUpdatedEvent($meeting));
    }

    /**
     * @param Meeting $meeting
     * @param Attachment[] $attachments
     */
    private function syncAttachments(Meeting $meeting, array $attachments)
    {
        $existing = $meeting->getAttachments();
        $current = [];

        foreach ($attachments as $attachment) {
            $id = $attachment->getId();
            $current[$id] = $attachment;
        }

        foreach ($existing as $attachment) {
            $id = $attachment->getId();

            if (in_array($id, array_keys($current))) {
                unset($current[$id]);
                continue;
            }

            $attachment->setMeeting(null);

            $this->attachments->store($attachment);
        }

        $meeting->addAttachments($current);
    }
}
