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

namespace Angelov\Eestec\Platform\Service;

use Angelov\Eestec\Platform\Model\Member;
use Angelov\Eestec\Platform\Repository\MeetingsRepositoryInterface;

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
     * Calculates the member's attendance rate for the weekly meetings.
     *
     * @param Member $member
     * @return int Attendance rate in percents
     */
    public function calculateAttendanceRateForMember(Member $member)
    {
        $memberJoinedDate = $this->membership->getJoinedDate($member);
        $oneYearAgo = (new \DateTime('now'))->modify('-1 year');

        if ($memberJoinedDate < $oneYearAgo) {
            // The member is part of the organization for more
            // than a year, count only the meetings in the last year

            $calculateFrom = $oneYearAgo;
        } else {
            // Otherwise, count the meetings that happened since
            // the member joined

            $calculateFrom = $memberJoinedDate;
        }

        $calculateTo = new \DateTime('now');

        $attended = $this->meetings->countAttendanceForMember($member, $calculateFrom, $calculateTo);
        $total = $this->meetings->countMeetingsInPeriod($calculateFrom, $calculateTo);

        if ($total == 0) {
            return 100; // this is a little weird case...
        }

        $rate = ($attended / $total) * 100;

        return (int)round($rate, 0);
    }
}
