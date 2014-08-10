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

use Angelov\Eestec\Platform\Model\Meeting;
use Angelov\Eestec\Platform\Model\Member;

interface MeetingsRepositoryInterface
{

    /**
     * @param  Meeting                               $meeting
     * @param  \Angelov\Eestec\Platform\Model\Member $creator
     * @param  array                                 $attendants
     * @return void
     */
    public function store(Meeting $meeting, Member $creator, array $attendants);

    public function all(array $withRelationships = []);

    public function countMeetingsInPeriod(\DateTime $from, \DateTime $to);

    public function get($id);

    public function getByPage($page, $limit, $with);

    /**
     * @todo rename the method to getTotalAttendanceDetails() or something similar
     * @return array
     */
    public function calculateAttendanceDetails();

    /**
     * Count the meetings in a given period, attended by the member
     *
     * @param Member $member
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function countAttendanceForMember(Member $member, \DateTime $from, \DateTime $to);

}
