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

use Angelov\Eestec\Platform\Exception\MemberNotFoundException;
use Angelov\Eestec\Platform\Model\Member;
use DateTime;
use DB;

class EloquentMembersRepository implements MembersRepositoryInterface
{

    /**
     * Returns all members from the database
     */
    public function all()
    {
        return Member::all();
    }

    public function destroy($id)
    {

        if (null == Member::find($id)) {
            throw new MemberNotFoundException();
        }

        Member::destroy($id);
    }

    public function store(Member $member)
    {
        $member->save();
    }

    public function get($id)
    {
        $member = Member::find($id);

        if ($member == null) {
            throw new MemberNotFoundException();
        }

        return $member;
    }

    public function getByPage($page = 1, $limit = 20)
    {
        $results = new \stdClass();
        $results->page = $page;
        $results->limit = $limit;
        $results->totalItems = 0;
        $results->items = array();

        $members = Member::skip($limit * ($page - 1))->take($limit)->get();

        $results->totalItems = Member::count();
        $results->items = $members->all();

        return $results;
    }

    public function countByMembershipStatus()
    {
        // MySQL and PostgreSQL have different methods for getting
        // the current date, so we can't do that inside the SQL
        // query.
        $currentDate = (new DateTime('now'))->format('Y-m-d');

        $result = (array)DB::select(
            '
                SELECT *
                FROM
                  (SELECT count(id) AS total
                   FROM members) AS tbl1,

                  (SELECT count(id) AS active
                   FROM members
                   WHERE id IN
                       (SELECT DISTINCT member_id
                        FROM fees
                        WHERE `to` > \'' . $currentDate . '\') ) AS tbl2;

            '
        )[0];

        return $result;

    }

    public function getByBirthdayDate(DateTime $date)
    {

        $members = Member::whereRaw(
            'EXTRACT(DAY from birthday) = ? and EXTRACT(MONTH from birthday) = ?',
            [$date->format('d'), $date->format('m')]
        )->get()->all();

        return $members;

    }

    public function getByIds(array $ids)
    {
        return Member::whereIn('id', $ids)->get()->all();
    }

    public function countPerFaculty()
    {

        $results = (array)DB::select(
            '
                        SELECT faculty,
                               count(id) AS members
                        FROM members
                        GROUP BY faculty
                        ORDER BY members DESC;
                    '
        );

        $list = [];

        foreach ($results as $current) {
            $current = (array)$current;
            $list[$current['faculty']] = $current['members'];
        }

        return $list;

    }

    public function countNewMembersPerMonth(DateTime $from, DateTime $to)
    {

        $from = $from->format("Y-m-d");
        $to = $to->format("Y-m-d");

        $res = DB::select(
            '
                        SELECT concat(year, "-", lpad(month, 2, "0")) AS month,
                               count(id) AS count
                        FROM
                          (SELECT id,
                                  email,
                                  extract(MONTH FROM joined) AS month,
                                  extract(YEAR FROM joined) AS year
                           FROM
                             (SELECT id,
                                     email,
                                     min(`from`) AS joined
                              FROM
                                (SELECT members.`id`,
                                        `email`,
                                        fees.`from`,
                                        `to`
                                 FROM `members`,
                                      `fees`
                                 WHERE members.id = member_id
                                   AND fees.`from` BETWEEN ? AND ?) AS tbl
                              GROUP BY id) AS tbl2) AS tbl3
                        GROUP BY concat(month, year);
                    ',
            array($from, $to)
        );

        /*array_walk($res, function (&$current) {
                $current = (array) $current;
        });*/

        $list = [];

        foreach ($res as &$current) {
            $current = (array)$current;
            $list[$current['month']] = $current['count'];
        }

        return $list;

    }

}
