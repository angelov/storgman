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

namespace Angelov\Eestec\Platform\Handlers\Commands\Fees;

use Angelov\Eestec\Platform\Membership\Commands\StoreFeeCommand;
use Angelov\Eestec\Platform\DateTime;
use Angelov\Eestec\Platform\Membership\Fee;
use Angelov\Eestec\Platform\Membership\Events\FeeWasProceededEvent;
use Angelov\Eestec\Platform\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
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

    public function handle(\Angelov\Eestec\Platform\Membership\Commands\StoreFeeCommand $command)
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
