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

namespace Angelov\Storgman\Meetings;

use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Meetings\Reports\MeetingAttendedReport;
use Angelov\Storgman\Meetings\Reports\MeetingsAttendanceDetailsForMemberReport;
use Angelov\Storgman\Meetings\Reports\MeetingsAttendedByMemberPerMonthReport;
use Angelov\Storgman\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Angelov\Storgman\Membership\MembershipService;
use InvalidArgumentException;

class MeetingsService
{
    protected $meetings;
    protected $members;
    protected $membership;

    public function __construct(MeetingsRepositoryInterface $meetings, MembersRepositoryInterface $members, MembershipService $membership)
    {
        $this->meetings = $meetings;
        $this->members = $members;
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
        $memberJoinedDate = $member->getJoiningDate();
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

    /**
     * @param Member $member
     * @return MeetingAttendedReport[]
     */
    public function latestMeetingsAttendanceStatusForMember(Member $member)
    {
        $meetings = $this->meetings->latest(10, ['attendants'], 'date');
        $reports = [];

        foreach ($meetings as $meeting) {
            $attendance = $meeting->wasAttendedBy($member);
            $reports[] = new MeetingAttendedReport($member, $meeting, $attendance);
        }

        return $reports;
    }

    /**
     * @param Member $member
     * @return MeetingsAttendedByMemberPerMonthReport
     */
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
     * @return int[]
     */
    public function parseAttendantsIds($attendants)
    {
        $ids = explode("|", $attendants);
        $ids = array_filter(
            $ids,
            function ($value) {
                return is_numeric($value);
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
            if (! $member instanceof Member) {
                throw new InvalidArgumentException("The elements of the array have to be Member instances");
            }

            $list .= $member->getId() ."|";
        }

        return $list;
    }

    /**
     * @param string $ids
     * @return Member[]
     */
    public function extractAttendants($ids)
    {
        $parsedIds = $this->parseAttendantsIds($ids);
        $attendants = $this->members->getByIds($parsedIds);

        return $attendants;
    }
}
