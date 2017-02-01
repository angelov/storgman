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

namespace Angelov\Storgman\Membership\Repositories;

use Angelov\Storgman\Core\Repositories\AbstractEloquentRepository;
use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Membership\Fee;
use Angelov\Storgman\Membership\Reports\ExpectedFeesPerMonthReport;
use Angelov\Storgman\Membership\Reports\PaidFeesPerMonthReport;
use DB;

class EloquentFeesRepository extends AbstractEloquentRepository implements FeesRepositoryInterface
{
    public function __construct(Fee $entity)
    {
        parent::__construct($entity);
    }

    public function store(Fee $fee)
    {
        $fee->save();
    }

    public function getSoonToExpire($count = 10)
    {
        $now = DateTime::nowAsDateString();
        $fees = $this->entity
            ->where('to_date', '>', $now)
            ->orderBy('to_date')
            ->take($count)
            ->get()
            ->all();

        return $fees;
    }

    public function calculateExpectedFeesPerMonth(DateTime $from, DateTime $to)
    {
        $to = $to->modify('last day of this month');

        $report = new ExpectedFeesPerMonthReport($from, $to);

        $res = DB::select(
            '
                SELECT concat(YEAR, \'-\', lpad(cast(MONTH AS CHAR(2)), 2, \'0\')) AS month,
                       count(*) AS count
                FROM
                  (SELECT extract(MONTH
                                  FROM to_date) AS MONTH,
                          extract(YEAR
                                  FROM to_date) AS YEAR
                   FROM
                     (SELECT to_date
                      FROM fees
                      WHERE to_date BETWEEN ? AND ?) tbl1) tbl2
                GROUP BY concat(MONTH, YEAR), MONTH, YEAR
                ORDER BY YEAR, MONTH
            ', [$from->toDateString(), $to->toDateString()]
        );

        /** @todo Similar code is duplicated across the project */
        foreach ($res as &$crnt) {
            $report->addMonth($crnt->month, (int) $crnt->count);
        }

        return $report;
    }

    /**
     *              /\
     * @todo These two queries can be combined
     *             \/
     */

    public function calculatePaidFeesPerMonth(DateTime $from, DateTime $to)
    {
        $report = new PaidFeesPerMonthReport($from, $to);

        $res = DB::select(
            '
                SELECT concat(YEAR, \'-\', lpad(cast(MONTH AS CHAR(2)), 2, \'0\')) AS month,
                       count(*) AS count
                FROM
                  (SELECT extract(MONTH
                                  FROM from_date) AS MONTH,
                          extract(YEAR
                                  FROM from_date) AS YEAR
                   FROM
                     (SELECT from_date
                      FROM fees
                      WHERE from_date BETWEEN ? AND ?) tbl1) tbl2
                GROUP BY concat(MONTH, YEAR), MONTH, YEAR
                ORDER BY YEAR, MONTH

            ', [$from->toDateString(), $to->toDateString()]
        );

        foreach ($res as &$crnt) {
            $report->addMonth($crnt->month, (int) $crnt->count);
        }

        return $report;
    }
}
