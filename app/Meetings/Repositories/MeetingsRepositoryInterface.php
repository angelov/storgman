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

namespace Angelov\Storgman\Meetings\Repositories;

use Angelov\Storgman\Core\Repositories\RepositoryInterface;
use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Meetings\Exceptions\NoPreviousMeetingException;
use Angelov\Storgman\Meetings\Meeting;
use Angelov\Storgman\Meetings\Reports\MeetingAttendantsTypeReport;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Core\Exceptions\ResourceNotFoundException;
use Angelov\Storgman\Meetings\Reports\MeetingsAttendanceDetailsReport;
use Angelov\Storgman\Meetings\Reports\MeetingsPerMonthReport;
use Carbon\Carbon;

interface MeetingsRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the meeting with the given ID
     *
     * @param int $id
     * @return Meeting
     * @throws ResourceNotFoundException
     */
    public function get($id);

    /**
     * Returns all meetings
     *
     * @param array $withRelationships
     * @return Meeting[]
     */
    public function all(array $withRelationships = []);

    /**
     * Stores a meeting
     *
     * @param  Meeting $meeting
     * @return void
     */
    public function store(Meeting $meeting);

    /**
     * Counts the meetings in a given date range
     *
     * @param Carbon $from
     * @param Carbon $to
     * @return int
     */
    public function countMeetingsInPeriod(Carbon $from, Carbon $to);

    /**
     * Calculates global attendance details.
     *
     * @todo rename the method to getTotalAttendanceDetails() or something similar
     * @return MeetingsAttendanceDetailsReport
     */
    public function calculateAttendanceDetails();

    /**
     * Count the meetings in a given period, attended by the member
     *
     * @param Member $member
     * @param Carbon $from
     * @param Carbon $to
     * @return int
     */
    public function countAttendanceForMember(Member $member, Carbon $from, Carbon $to);

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

    /**
     * Returns the latest N meetings
     *
     * @param $count
     * @param array $withRelationships
     * @param string $orderByField
     * @return Meeting[]
     */
    public function latest($count, array $withRelationships = [], $orderByField = 'date');

    /**
     * @return int
     */
    public function getAverageNumberOfAttendants();

    /**
     * @param Meeting $meeting
     * @return Meeting
     * @throws NoPreviousMeetingException
     */
    public function getPreviousMeeting(Meeting $meeting);

    /**
     * @param Meeting $meeting
     * @return MeetingAttendantsTypeReport
     */
    public function getAttendantsTypeForMeeting(Meeting $meeting);
}
