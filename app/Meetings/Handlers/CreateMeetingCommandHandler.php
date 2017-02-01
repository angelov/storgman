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

use Angelov\Storgman\Meetings\Attachments\Attachment;
use Angelov\Storgman\Meetings\Attachments\Exceptions\NotOwnerOfAttachmentException;
use Angelov\Storgman\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Storgman\Meetings\Commands\CreateMeetingCommand;
use Angelov\Storgman\Meetings\Events\MeetingWasCreatedEvent;
use Angelov\Storgman\Meetings\Meeting;
use Angelov\Storgman\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class CreateMeetingCommandHandler
{
    protected $meetings;
    protected $members;
    protected $attachments;
    protected $events;

    public function __construct(
        MeetingsRepositoryInterface $meetings,
        MembersRepositoryInterface $members,
        AttachmentsRepositoryInterface $attachments,
        Dispatcher $events)
    {
        $this->meetings = $meetings;
        $this->members = $members;
        $this->attachments = $attachments;
        $this->events = $events;
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

        $attachments = $this->attachments->getByIds($command->getAttachments());
        $this->checkAttachmentOwnership($creator, $attachments);
        $meeting->addAttachments($attachments);

        $this->meetings->store($meeting);

        $this->events->fire(new MeetingWasCreatedEvent($meeting, $command->getNotifyMembers()));

        return $meeting;
    }

    /**
     * @param Member $creator
     * @param Attachment[] $attachments
     * @throws NotOwnerOfAttachmentException
     */
    private function checkAttachmentOwnership(Member $creator, array $attachments)
    {
        foreach ($attachments as $attachment) {
            if ($attachment->getOwner()->getId() != $creator->getId()) {
                throw new NotOwnerOfAttachmentException("You must own the attachment to add it to a meeting");
            }
        }
    }
}
