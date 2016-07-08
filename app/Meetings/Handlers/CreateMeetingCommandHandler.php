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

use Angelov\Eestec\Platform\Meetings\Attachments\Attachment;
use Angelov\Eestec\Platform\Meetings\Attachments\Exceptions\NotOwnerOfAttachmentException;
use Angelov\Eestec\Platform\Meetings\Attachments\Repositories\AttachmentsRepositoryInterface;
use Angelov\Eestec\Platform\Meetings\Commands\CreateMeetingCommand;
use Angelov\Eestec\Platform\Meetings\Events\MeetingWasCreatedEvent;
use Angelov\Eestec\Platform\Meetings\Meeting;
use Angelov\Eestec\Platform\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Member;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
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
