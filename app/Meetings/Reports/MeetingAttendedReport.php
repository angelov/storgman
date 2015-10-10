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

namespace Angelov\Eestec\Platform\Meetings\Reports;

use Angelov\Eestec\Platform\Meetings\Meeting;
use Angelov\Eestec\Platform\Members\Member;

class MeetingAttendedReport
{
    protected $meeting;
    protected $member;
    protected $attended;

    /**
     * @param Member $member
     * @param \Angelov\Eestec\Platform\Meetings\Meeting $meeting
     * @param boolean $attended
     */
    public function __construct(Member $member, Meeting $meeting, $attended)
    {
        $this->member = $member;
        $this->meeting = $meeting;
        $this->attended = $attended;
    }

    /**
     * @param boolean $attended
     */
    public function setAttended($attended)
    {
        $this->attended = $attended;
    }

    /**
     * @return boolean
     */
    public function getAttended()
    {
        return $this->attended;
    }

    /**
     * @param \Angelov\Eestec\Platform\Meetings\Meeting $meeting
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;
    }

    /**
     * @return \Angelov\Eestec\Platform\Meetings\Meeting
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * @param \Angelov\Eestec\Platform\Members\Member $member
     */
    public function setMember($member)
    {
        $this->member = $member;
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }
}
