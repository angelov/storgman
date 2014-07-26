<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Service;

use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;
use DateTime;

class MembersStatisticsService {

    protected $members;

    public function __construct(MembersRepositoryInterface $members) {
        $this->members = $members;
    }

    /**
     * Returns an array with num. of new members in
     * each of the last 12 months.
     *
     * example:
     *   array (size=12)
     *      'Aug 2013' => int 1
     *      'Sep 2013' => int 0
     *      'Oct 2013' => int 0
     *      ...
     *      'Jul 2014' => int 3
     *
     * @return array
     */
    public function newMembersMonthlyLastYear() {

        $from = (new DateTime('now'))
                    ->modify('first day of this month')
                    ->modify('-1 year')
                    ->modify('+1 month');
        $to = new DateTime('now');

        $res = $this->members->countNewMembersPerMonth($from, $to);

        $year = 2013;
        $count = 0;

        $list = [];

        for ($m = $from->format('m'); $m <= 12; $m++){

            $count++;

            $dt = new DateTime("1-". $m ."-". $year);
            $month = $dt->format('M');
            $full = $dt->format('Y-m');

            $list[$month ." ". $year] = isset($res[$full]) ? (int) $res[$full] : 0;

            if ($m == 12 && $count < 12) {
                $m = 0;
                $year++;
            }

            if ($count == 12) {
                break;
            }

        }

        return $list;

    }

}