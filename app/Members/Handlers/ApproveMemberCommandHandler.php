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

namespace Angelov\Storgman\Members\Handlers;

use Angelov\Storgman\Members\Commands\ApproveMemberCommand;
use Angelov\Storgman\Members\Events\MemberWasApprovedEvent;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class ApproveMemberCommandHandler
{
    protected $members;
    protected $events;

    public function __construct(MembersRepositoryInterface $members, Dispatcher $events)
    {
        $this->members = $members;
        $this->events = $events;
    }

    public function handle(ApproveMemberCommand $command)
    {
        $id = $command->getMemberId();

        $member = $this->members->get($id);
        $member->setApproved(true);

        $this->members->store($member);

        $this->events->fire(new MemberWasApprovedEvent($member));
    }
}
