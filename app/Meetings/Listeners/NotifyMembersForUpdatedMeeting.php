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

namespace Angelov\Eestec\Platform\Meetings\Listeners;

use Angelov\Eestec\Platform\Meetings\Events\MeetingWasUpdatedEvent;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;

class NotifyMembersForUpdatedMeeting
{
    protected $mailer;
    protected $members;

    public function __construct(Mailer $mailer, MembersRepositoryInterface $members)
    {
        $this->mailer = $mailer;
        $this->members = $members;
    }

    public function handle(MeetingWasUpdatedEvent $event)
    {
        $members = $this->members->all();
        $meeting = $event->getMeeting();

        foreach ($members as $member) {

            if (! $member->isApproved()) {
                continue;
            }

            $this->mailer->send('emails.meetings.updated', compact('member', 'meeting'), function (Message $message) use ($member) {
                $message->to($member->getEmail())->subject('Meeting details updated!');
            });
        }
    }
}