<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Meetings\Reports;

use JsonSerializable;

class MeetingsAttendedByMemberPerMonthReport implements JsonSerializable
{
    protected $total = [];
    protected $attended = [];
    protected $months = [];

    public function __construct(array $months, array $total, array $attended)
    {
        $this->attended = $attended;
        $this->months = $months;
        $this->total = $total;
    }

    /**
     * @return array
     */
    public function getAttended()
    {
        return $this->attended;
    }

    /**
     * @return array
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * @return array
     */
    public function getTotal()
    {
        return $this->total;
    }

    public function jsonSerialize()
    {
        $data = [
            "months" => $this->getMonths(),
            "total" => $this->getTotal(),
            "attended" => $this->getAttended()
        ];

        return $data;
    }
}
