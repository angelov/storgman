<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Meetings\Repositories;

use Angelov\Eestec\Platform\Core\Repositories\AbstractEloquentRepository;
use Angelov\Eestec\Platform\Core\DateTime;
use Angelov\Eestec\Platform\Meetings\Exceptions\NoPreviousMeetingException;
use Angelov\Eestec\Platform\Meetings\Meeting;
use Angelov\Eestec\Platform\Meetings\Reports\MeetingAttendantsTypeReport;
use Angelov\Eestec\Platform\Members\Member;
use Angelov\Eestec\Platform\Meetings\Reports\MeetingsAttendanceDetailsReport;
use Angelov\Eestec\Platform\Meetings\Reports\MeetingsPerMonthReport;
use Carbon\Carbon;
use DB;

class EloquentMeetingsRepository extends AbstractEloquentRepository implements MeetingsRepositoryInterface
{
    public function __construct(Meeting $meeting)
    {
        parent::__construct($meeting);
    }

    public function store(Meeting $meeting)
    {
        $meeting->save();
    }

    public function countMeetingsInPeriod(Carbon $from, Carbon $to)
    {
        $from = $from->toDateString();
        $to = $to->toDateString();

        return Meeting::whereBetween('date', array($from, $to))->count();
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

        $report = new MeetingsAttendanceDetailsReport();

        $report->setMeetings($result['meetings'] ? : 0);
        $report->setAttendants($result['attendants'] ? : 0);
        $report->setAverage($result['average'] ? : 0);

        return $report;
    }

    public function countAttendanceForMember(Member $member, Carbon $from, Carbon $to)
    {
        $from = $from->toDateString();
        $to = $to->toDateString();

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
            array($member->getId(), $from, $to)
        )[0];

        return $result->attended;
    }

    public function countMeetingsPerMonth(DateTime $from, DateTime $to)
    {
        $report = new MeetingsPerMonthReport($from, $to);
        $from = $from->toDateString();
        $to = $to->toDateString();

        $res = DB::select(
            '
                SELECT concat(YEAR, \'-\', lpad(cast(MONTH AS CHAR(2)), 2, \'0\')) AS month,
                       count(id) AS count
                FROM
                  ( SELECT id,
                           extract(MONTH
                                   FROM date) AS MONTH,
                           extract(YEAR
                                   FROM date) AS YEAR
                   FROM
                     ( SELECT id, date
                      FROM meetings
                      WHERE date BETWEEN ? AND ?) tbl1) tbl2
                GROUP BY YEAR,
                         MONTH
            ',
            [$from, $to]
        );

        foreach ($res as &$current) {
            $current = (array)$current;
            $report->addMonth($current["month"], (int)$current["count"]);
        };

        return $report;
    }

    public function countAttendedMeetingsByMemberPerMonth(Member $member, DateTime $from, DateTime $to)
    {
        $report = new MeetingsPerMonthReport($from, $to);
        $from = $from->toDateString();
        $to = $to->toDateString();

        $res = DB::select(
            '
                SELECT concat(YEAR, \'-\', lpad(cast(MONTH AS CHAR(2)), 2, \'0\')) AS month,
                       count(id) AS count
                FROM
                  (SELECT id,
                          extract(MONTH
                                  FROM date) AS MONTH,
                          extract(YEAR
                                  FROM date) AS YEAR
                   FROM
                     (SELECT id, date
                      FROM meetings
                      WHERE id IN
                          (SELECT meeting_id
                           FROM meeting_member
                           WHERE member_id = ?)
                        AND date BETWEEN ? AND ? ) tbl1) tbl2
                GROUP BY YEAR,
                         MONTH
            ',
            [$member->getId(), $from, $to]
        );

        foreach ($res as &$current) {
            $current = (array)$current;
            $report->addMonth($current["month"], (int)$current["count"]);
        };

        return $report;
    }

    public function getAverageNumberOfAttendants()
    {
        $result = DB::select(
          '
                  SELECT avg(attendants) AS average
                  FROM
                    (SELECT count(meeting_member.id) AS attendants
                     FROM meetings,
                          meeting_member
                     WHERE meetings.id = meeting_member.meeting_id
                       AND meetings.date <= now()
                     GROUP BY meeting_member.meeting_id) AS na;
        '
        );

        $average = (int) round($result[0]->average);

        return $average;
    }

    // @todo optimize
    public function getPreviousMeeting(Meeting $meeting)
    {
        $result = DB::select(
            '
                    SELECT id,
                      (SELECT id
                       FROM meetings e2
                       WHERE e2.date < e1.date
                       ORDER BY date DESC LIMIT 1) AS previous_meeting_id
                    FROM meetings e1
                    WHERE id = ?

            ', [$meeting->getId()]
        );

        $prevId = (int) $result[0]->previous_meeting_id;

        if (! $prevId) {
            throw new NoPreviousMeetingException();
        }

        return Meeting::find($prevId);
    }

    public function getAttendantsTypeForMeeting(Meeting $meeting)
    {
        try {
            $previous = $this->getPreviousMeeting($meeting);
        } catch (NoPreviousMeetingException $e) {
            $attendants = count($meeting->getAttendants());
            return new MeetingAttendantsTypeReport($meeting, $attendants, 0);
        }

        $result = DB::select(
            '
                SELECT sum(CASE WHEN cnt > 1 THEN 1 ELSE 0 END) AS returning
                FROM
                  (SELECT member_id,
                          count(member_id) AS cnt
                   FROM
                     (SELECT meeting_member.member_id
                      FROM meetings,
                           meeting_member
                      WHERE meetings.id = meeting_member.meeting_id
                        AND meetings.id IN (?, ?)) attendants
                   GROUP BY member_id) attendants_count
            ', [$meeting->getId(), $previous->getId()]
        );

        $result = $result[0];
        $total = count($meeting->getAttendants());
        $returning = (int) $result->returning;
        $new = $total - $returning;


        return new MeetingAttendantsTypeReport($meeting, $new, $returning);
    }
}
