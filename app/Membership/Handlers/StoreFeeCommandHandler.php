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

namespace Angelov\Storgman\Membership\Handlers;

use Angelov\Storgman\Membership\Commands\StoreFeeCommand;
use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Membership\Fee;
use Angelov\Storgman\Membership\Events\FeeWasProceededEvent;
use Angelov\Storgman\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class StoreFeeCommandHandler
{
    protected $members;
    protected $fees;
    protected $events;

    public function __construct(FeesRepositoryInterface $fees, MembersRepositoryInterface $members, Dispatcher $events)
    {
        $this->members = $members;
        $this->fees = $fees;
        $this->events = $events;
    }

    public function handle(StoreFeeCommand $command)
    {
        $fee = new Fee();

        $from = new DateTime($command->getFromDate());
        $to = new DateTime($command->getToDate());

        $fee->setFromDate($from);
        $fee->setToDate($to);

        $memberId = $command->getMemberId();
        $member = $this->members->get($memberId);

        $fee->setMember($member);

        $this->fees->store($fee);

        $this->events->fire(new FeeWasProceededEvent($fee));
    }
}
