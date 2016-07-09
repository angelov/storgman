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

namespace Angelov\Eestec\Platform\Meetings\Reports;

use Angelov\Eestec\Platform\Meetings\Meeting;
use JsonSerializable;

class MeetingAttendantsTypeReport implements JsonSerializable
{
    protected $meeting;
    protected $new;
    protected $returning;

    public function __construct(Meeting $meeting, $new, $returning)
    {
        $this->meeting = $meeting;
        $this->new = $new;
        $this->returning = $returning;
    }

    public function getMeeting()
    {
        return $this->meeting;
    }

    public function getNew()
    {
        return $this->new;
    }

    public function getReturning()
    {
        return $this->returning;
    }

    function jsonSerialize()
    {
        return [
            ['New', $this->getNew()],
            ['Returning', $this->getReturning()]
        ];
    }
}