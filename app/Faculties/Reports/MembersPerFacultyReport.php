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

namespace Angelov\Storgman\Faculties\Reports;

use Angelov\Storgman\Faculties\Faculty;
use JsonSerializable;

// @todo can be improved
class MembersPerFacultyReport implements JsonSerializable
{
    protected $faculties = [];

    public function addFaculty(Faculty $faculty, $count)
    {
        $this->faculties[] = [$faculty, $count];
    }

    public function getFaculties()
    {
        return $this->faculties;
    }

    public function jsonSerialize()
    {
        $faculties = [];

        foreach ($this->faculties as $current) {
            $count = $current[1];
            /** @var Faculty $faculty */
            $faculty = $current[0];
            $faculties[] = [$faculty->getAbbreviation(), (int)$count];
        }

        return $faculties;
    }
}
