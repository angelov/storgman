<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Members;

use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;

class MembersStatisticsService
{
    protected $members;

    public function __construct(MembersRepositoryInterface $members)
    {
        $this->members = $members;
    }

    /**
     * Returns an array with num. of new members in
     * each of the last 12 months.
     */
    public function newMembersMonthlyLastYear()
    {
        $from = DateTime::twelveMonthsAgo(true);
        $to = new DateTime('now');

        $report = $this->members->countNewMembersPerMonth($from, $to);

        return $report;
    }
}
