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

namespace Angelov\Eestec\Platform\Repository;

use Angelov\Eestec\Platform\DateTime;
use Angelov\Eestec\Platform\Exception\NoFeesException;
use Angelov\Eestec\Platform\Model\Fee;
use Angelov\Eestec\Platform\Model\Member;
use Angelov\Eestec\Platform\Report\ExpectedFeesPerMonthReport;
use Angelov\Eestec\Platform\Report\PaidFeesPerMonthReport;
use DB;

class EloquentFeesRepository extends AbstractEloquentRepository implements FeesRepositoryInterface
{
    public function __construct(Fee $model)
    {
        $this->model = $model;
    }

    public function store(Fee $fee, Member $member)
    {
        $member->fees()->save($fee);
    }

    public function getFeesForMember(Member $member)
    {
        $fees = $member->fees()->get()->all();

        return $fees;
    }

    public function getLatestFeeForMember(Member $member)
    {
        return $this->getFeeByOrder($member, "DESC");
    }

    public function getFirstFeeForMember(Member $member)
    {
        return $this->getFeeByOrder($member, "ASC");
    }

    private function getFeeByOrder(Member $member, $order)
    {
        $fee = $member->fees()->orderBy('to_date', $order)->first();

        if ($fee == null) {
            throw new NoFeesException();
        }

        return $fee;
    }

    public function getFeeMember(Fee $fee)
    {
        return $fee->member;
    }

    public function getSoonToExpire($count = 10)
    {
        $now = DateTime::nowAsDateString();
        $fees = $this->model
            ->where('to_date', '>', $now)
            ->orderBy('to_date')
            ->take($count)
            ->get()
            ->all();

        return $fees;
    }

    public function calculateExpectedFeesPerMonth(DateTime $from, DateTime $to)
    {
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
