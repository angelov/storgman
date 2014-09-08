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
use Angelov\Eestec\Platform\Exception\ResourceNotFoundException;
use Angelov\Eestec\Platform\Model\Meeting;
use Angelov\Eestec\Platform\Model\Member;
use Angelov\Eestec\Platform\Report\MeetingsAttendanceDetailsReport;

interface MeetingsRepositoryInterface
{
    /**
     * Stores the meeting and creates a list of attendants
     *
     * @param  Meeting  $meeting
     * @param  Member   $creator
     * @param  array    $attendants
     * @return void
     */
    public function store(Meeting $meeting, Member $creator, array $attendants);

    /**
     *  Returns all meetings
     *
     * @param array $withRelationships
     * @return array
     */
    public function all(array $withRelationships = []);

    /**
     * Returns the latest N meetings
     *
     * @param $count
     * @param array $withRelationships
     * @return array
     */
    public function latest($count, array $withRelationships = []);

    /**
     * Deletes a specific member from the storage
     *
     * @param $id int
     * @return void
     * @throws ResourceNotFoundException
     */
    public function destroy($id);

    /**
     * Counts the meetings in a given date range
     *
     * @param DateTime $from
     * @param DateTime $to
     * @return int
     */
    public function countMeetingsInPeriod(DateTime $from, DateTime $to);

    /**
     * Returns a specific meeting
     *
     * @param int $id
     * @return Meeting
     */
    public function get($id);

    /**
     * Returns array of meetings for a specific page
     *
     * @param int $page
     * @param int $limit
     * @param array $withRelationships
     * @return \stdClass
     */
    public function getByPage($page, $limit, array $withRelationships);

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

}
