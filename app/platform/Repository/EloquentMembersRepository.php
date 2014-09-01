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

use Angelov\Eestec\Platform\Model\Member;
use Angelov\Eestec\Platform\Report\MembershipStatusReport;
use Angelov\Eestec\Platform\Report\MembersPerFacultyReport;
use Angelov\Eestec\Platform\Report\NewMembersPerMonthReport;
use DateTime;
use DB;

class EloquentMembersRepository extends AbstractEloquentRepository implements MembersRepositoryInterface
{
    public function __construct(Member $model)
    {
        $this->model = $model;
    }

    public function store(Member $member)
    {
        $member->save();
    }

    public function countByMembershipStatus()
    {
        // The query currently works only with PostgreSQL
        $result = (array)DB::select(
            '
                SELECT *
                FROM
                  (SELECT count(id) AS total
                   FROM members) tbl1,
                  (SELECT count(id) AS active
                   FROM members
                   WHERE id IN
                       (SELECT DISTINCT member_id
                        FROM fees
                        WHERE cast("to" AS DATE) > current_date)) tbl2
            '
        )[0];

        $report = new MembershipStatusReport($result['total'], $result['active']);

        return $report;
    }

    public function getByBirthdayDate(DateTime $date)
    {
        $members = Member::whereRaw(
            'EXTRACT(DAY from birthday) = ? and EXTRACT(MONTH from birthday) = ?',
            [$date->format('d'), $date->format('m')]
        )->get()->all();

        return $members;
    }

    public function countPerFaculty()
    {

        // The query works with both MySQL and PostgreSQL
        $results = (array)DB::select(
            '
                SELECT faculty,
                       count(id) AS members
                FROM members
                GROUP BY faculty
                ORDER BY members DESC;
            '
        );

        $report = new MembersPerFacultyReport();

        foreach ($results as $current) {
            $current = (array)$current;
            $report->addFaculty($current["faculty"], $current["members"]);
        }

        return $report;

    }

    public function countNewMembersPerMonth(DateTime $from, DateTime $to)
    {
        $report = new NewMembersPerMonthReport($from, $to);

        $from = $from->format("Y-m-d");
        $to = $to->format("Y-m-d");

        // The query currently works only with PostgreSQL
        $res = DB::select(
            '
                SELECT concat(YEAR, \'-\', lpad(cast(MONTH AS TEXT), 2, \'0\')) AS month,
                       count(id) AS count
                FROM
                  (SELECT id,
                          email,
                          extract(MONTH
                                  FROM joined) AS MONTH,
                          extract(YEAR
                                  FROM joined) AS YEAR
                   FROM
                     (SELECT id,
                             email,
                             min("from") AS joined
                      FROM
                        (SELECT members."id",
                                members."email",
                                fees."from",
                                fees. "to"
                         FROM members,
                              fees
                         WHERE members.id = member_id
                           AND fees."from" BETWEEN ? AND ?) tbl
                      GROUP BY id,
                               email) AS tbl2) tbl3
                GROUP BY concat(MONTH, YEAR),
                         MONTH,
                         YEAR;
            ',
            array($from, $to)
        );

        foreach ($res as &$current) {
            $current = (array)$current;
            $report->addMonth($current["month"], $current["count"]);
        }

        return $report;
    }

}
