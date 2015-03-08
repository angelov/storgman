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

use Angelov\Eestec\Platform\Commands\Members\CreateMemberCommand;
use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Events\Members\MemberJoinedEvent;
use Angelov\Eestec\Platform\Populators\MembersPopulator;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class CreateMemberCommandHandler
{
    protected $members;
    protected $populator;
    protected $events;

    public function __construct(MembersRepositoryInterface $members, MembersPopulator $populator, Dispatcher $events)
    {
        $this->members = $members;
        $this->populator = $populator;
        $this->events = $events;
    }

    public function handle(CreateMemberCommand $command)
    {
        $member = new Member();
        $data = $command->getMemberData();

        $this->populator->populateFromArray($member, $data);

        if ($command->shouldBeApproved()) { // Member was added by a board member

            $member->setApproved(true);

        } else { // The member created his account

            $this->events->fire(new MemberJoinedEvent($member));

        }

        $this->members->store($member);
    }
}
