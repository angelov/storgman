<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Handlers\Commands\Members;

use Angelov\Eestec\Platform\Commands\Members\ApproveMemberCommand;
use Angelov\Eestec\Platform\Events\Members\MemberWasApprovedEvent;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
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
