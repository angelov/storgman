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

use Angelov\Eestec\Platform\Exception\MeetingNotFoundException;
use Angelov\Eestec\Platform\Model\Meeting;
use Angelov\Eestec\Platform\Model\Member;
use DB;

class EloquentMeetingsRepository implements MeetingsRepositoryInterface
{

    public function store(Meeting $meeting, Member $creator, array $attendants)
    {
        $meeting->created_by = $creator->id;
        $meeting->save();
        $meeting->attendants()->saveMany($attendants);
    }

    public function all(array $withRelationships = [])
    {
        return Meeting::with($withRelationships)->get()->all();
    }

    public function countMeetingsInPeriod(\DateTime $from, \DateTime $to) {
        $from = $from->format('Y-m-d');
        $to = $to->format('Y-m-d');

        return Meeting::whereBetween('date', array($from, $to))->count();
    }

    public function get($id)
    {
        $meeting = Meeting::find($id);

        if ($meeting == null) {
            throw new MeetingNotFoundException();
        }

        return $meeting;
    }

    public function calculateAttendanceDetails()
    {

        // The query works with both MySQL and PostgreSQL
        $result = (array)DB::select(
            '
                SELECT tbl2.total_meetings AS meetings,
                       total_attendants AS attendants,
                       round(total_attendants/tbl2.total_meetings) AS average
                FROM
                  (SELECT sum(details.attendants) AS total_attendants
                   FROM
                     (SELECT meeting_id AS meeting,
                             count(member_id) AS attendants
                      FROM meeting_member
                      GROUP BY meeting_id) AS details) AS tbl1,

                  (SELECT count(id) AS total_meetings
                   FROM meetings) AS tbl2
            '
        )[0];

        $att['meetings'] = $result['meetings'] ? : 0;
        $att['attendants'] = $result['attendants'] ? : 0;
        $att['average'] = $result['average'] ? : 0;

        return $att;

    }

    public function countAttendanceForMember(Member $member, \DateTime $from, \DateTime $to)
    {

        $from = $from->format("Y-m-d");
        $to = $to->format("Y-m-d");

        // The query works with both MySQL and PostgreSQL
        $result = DB::select(
            '
                SELECT count(meeting_id) AS attended
                FROM meetings,
                     meeting_member
                WHERE meeting_id=meetings.id
                  AND member_id = ?
                  AND date BETWEEN ? AND ?
            ',
            array($member->id, $from, $to)
        )[0];

        return $result->attended;

    }

}
