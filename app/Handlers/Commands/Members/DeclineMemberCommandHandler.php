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

use Angelov\Eestec\Platform\Members\Commands\DeclineMemberCommand;
use Angelov\Eestec\Platform\Members\Commands\DeleteMemberCommand;
use Angelov\Eestec\Platform\Members\Events\MemberWasDeclinedEvent;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Events\Dispatcher as EventsDispatcher;

class DeclineMemberCommandHandler
{
    protected $commandBus;
    protected $events;
    protected $members;

    public function __construct(MembersRepositoryInterface $members, Dispatcher $commandBus, EventsDispatcher $events)
    {
        $this->commandBus = $commandBus;
        $this->events = $events;
        $this->members = $members;
    }

    public function handle(DeclineMemberCommand $command)
    {
        $id = $command->getMemberId();

        $this->commandBus->dispatch(new DeleteMemberCommand($id));

        $member = $this->members->get($id);

        $this->events->fire(new MemberWasDeclinedEvent($member));
    }
}
