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

namespace Angelov\Storgman\Faculties;

use Angelov\Storgman\Members\Member;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    public function setTitle($title)
    {
        $this->setAttribute('title', $title);
    }

    public function getAbbreviation()
    {
        return $this->getAttribute('abbreviation');
    }

    public function setAbbreviation($abbreviation)
    {
        $this->setAttribute('abbreviation', $abbreviation);
    }

    public function getUniversity()
    {
        return $this->getAttribute('university');
    }

    public function setUniversity($university)
    {
        $this->setAttribute('university', $university);
    }

    public function isEnabled()
    {
        return $this->getAttribute('enabled') == true;
    }

    public function setEnabled($enabled)
    {
        $this->setAttribute('enabled', $enabled === true);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function hasMembers()
    {
        return $this->members()->count() > 0;
    }

    public function __toString()
    {
        return $this->getTitle();
    }
}
