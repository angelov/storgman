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

namespace Angelov\Storgman\Meetings\Listeners;

use Angelov\Storgman\Meetings\Events\MeetingWasCreatedEvent;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Mail\Message;

class NotifyMembersForNewMeeting
{
    protected $mailer;
    protected $members;

    public function __construct(Mailer $mailer, MembersRepositoryInterface $members)
    {
        $this->mailer = $mailer;
        $this->members = $members;
    }

    public function handle(MeetingWasCreatedEvent $event)
    {
        if (! $event->shouldNotifyMembers()) {
            return;
        }

        $members = $this->members->all();
        $meeting = $event->getMeeting();

        foreach ($members as $member) {

            if (! $member->isApproved()) {
                continue;
            }

            $this->mailer->send('emails.meetings.announced', compact('member', 'meeting'), function (Message $message) use ($member) {
                $message->to($member->getEmail())->subject('New meeting was announced!');
            });
        }
    }
}