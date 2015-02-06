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

namespace Angelov\Eestec\Platform\Repositories;

use Angelov\Eestec\Platform\DateTime;
use Angelov\Eestec\Platform\Entities\Meeting;
use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Reports\MeetingsAttendanceDetailsReport;
use Angelov\Eestec\Platform\Reports\MeetingsPerMonthReport;

interface MeetingsRepositoryInterface extends RepositoryInterface
{
    /**
     * Stores the meeting and creates a list of attendants
     *
     * @param  Meeting $meeting
     * @param  Member $creator
     * @param  array    $attendants
     * @return void
     */
    public function store(Meeting $meeting, Member $creator, array $attendants = []);

    /**
     * Updates the meeting's attendants list
     *
     * @param Meeting $meeting
     * @param array $attendants
     * @return void
     */
    public function updateAttendantsList(Meeting $meeting, array $attendants);

    /**
     * Counts the meetings in a given date range
     *
     * @param DateTime $from
     * @param DateTime $to
     * @return int
     */
    public function countMeetingsInPeriod(DateTime $from, DateTime $to);

    /**
     * Calculates global attendance details.
     *
     * @todo rename the method to getTotalAttendanceDetails() or something similar
     * @return MeetingsAttendanceDetailsReport
     */
    public function calculateAttendanceDetails();

    /**
     * Returns array of members who attended the meeting
     *
     * @param Meeting $meeting
     * @return array
     */
    public function getMeetingAttendants(Meeting $meeting);

    /**
     * Count the meetings in a given period, attended by the member
     *
     * @param Member $member
     * @param DateTime $from
     * @param DateTime $to
     * @return int
     */
    public function countAttendanceForMember(Member $member, DateTime $from, DateTime $to);

    /**
     * Count the total number of meetings per month
     *
     * @param DateTime $from
     * @param DateTime $to
     * @return MeetingsPerMonthReport
     */
    public function countMeetingsPerMonth(DateTime $from, DateTime $to);

    /**
     * Count the number of meetings attended by member per month
     *
     * @param Member $member
     * @param DateTime $from
     * @param DateTime $to
     * @return MeetingsPerMonthReport
     */
    public function countAttendedMeetingsByMemberPerMonth(Member $member, DateTime $from, DateTime $to);
}
