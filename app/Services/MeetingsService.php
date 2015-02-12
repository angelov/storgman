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

namespace Angelov\Eestec\Platform\Services;

use Angelov\Eestec\Platform\DateTime;
use Angelov\Eestec\Platform\Entities\Meeting;
use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Reports\MeetingAttendedReport;
use Angelov\Eestec\Platform\Reports\MeetingsAttendanceDetailsForMemberReport;
use Angelov\Eestec\Platform\Reports\MeetingsAttendedByMemberPerMonthReport;
use Angelov\Eestec\Platform\Repositories\MeetingsRepositoryInterface;

class MeetingsService
{
    protected $meetings;
    protected $membership;

    public function __construct(MeetingsRepositoryInterface $meetings, MembershipService $membership)
    {
        $this->meetings = $meetings;
        $this->membership = $membership;
    }

    /**
     * Calculates the member's attendance details for the weekly meetings.
     *
     * @param Member $member
     * @return MeetingsAttendanceDetailsForMemberReport
     */
    public function calculateAttendanceDetailsForMember(Member $member)
    {
        $memberJoinedDate = $this->membership->getJoinedDate($member);
        $oneYearAgo = DateTime::oneYearAgo();

        if ($memberJoinedDate < $oneYearAgo) {
            // The member is part of the organization for more
            // than a year, count only the meetings in the last year

            $calculateFrom = $oneYearAgo;
        } else {
            // Otherwise, count the meetings that happened since
            // the member joined

            $calculateFrom = $memberJoinedDate;
        }

        $calculateTo = new DateTime();

        $attended = $this->meetings->countAttendanceForMember($member, $calculateFrom, $calculateTo);
        $total = $this->meetings->countMeetingsInPeriod($calculateFrom, $calculateTo);

        $report = new MeetingsAttendanceDetailsForMemberReport($attended, $total - $attended);

        return $report;
    }

    public function latestMeetingsAttendanceStatusForMember($member)
    {
        $meetings = $this->meetings->latest(10, ['attendants']);
        $reports = [];

        foreach ($meetings as $meeting) {
            $attendance = $this->memberHasAttendedMeeting($member, $meeting);
            $reports[] = new MeetingAttendedReport($member, $meeting, $attendance);
        }

        return $reports;
    }

    public function memberHasAttendedMeeting(Member $member, Meeting $meeting)
    {
        $ids = [];
        foreach ($this->meetings->getMeetingAttendants($meeting) as $attendant) {
            $ids[] = $attendant->id;
        }

        return in_array($member->id, $ids);
    }

    public function calculateMonthlyAttendanceDetailsForMember(Member $member)
    {
        $begin = DateTime::twelveMonthsAgo(true);
        $end = new DateTime();

        $total = $this->meetings->countMeetingsPerMonth($begin, $end);
        $attended = $this->meetings->countAttendedMeetingsByMemberPerMonth($member, $begin, $end);

        $report = new MeetingsAttendedByMemberPerMonthReport(
            $total->getMonthsTitles(),
            $total->getMonthsValues(),
            $attended->getMonthsValues()
        );

        return $report;
    }

    /**
     * Receives a string that contains the attendants' IDs and
     * converts it to an array.
     *
     * @param $attendants
     * @return array
     */
    public function parseAttendantsIds($attendants)
    {
        $ids = explode("|", $attendants);
        $ids = array_filter(
            $ids,
            function ($value) {
                return $value != '';
            }
        );

        foreach ($ids as &$id) {
            $id = intval($id);
        }

        return $ids;
    }

    /**
     * Serialize the attendants' IDs in one string
     *
     * @param Member[] $attendants
     * @return string
     */
    public function prepareAttendantsIds(array $attendants)
    {
        $list = '|';

        foreach ($attendants as $member) {
            $list .= $member->id ."|";
        }

        return $list;
    }
}
