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

namespace Angelov\Storgman\LocalCommittees\Cities;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getName()
    {
        return $this->getAttribute('name');
    }

    public function setName($name)
    {
        $this->setAttribute('name', $name);
    }

    public function getCountry()
    {
        return $this->getAttribute('country');
    }

    public function setCountry($country)
    {
        $this->setAttribute('country', $country);
    }

    public function getLocation()
    {
        return new Location(
            $this->getAttribute('latitude'),
            $this->getAttribute('longitude')
        );
    }

    public function setLocation(Location $location)
    {
        $this->setAttribute('latitude', $location->getLatitude());
        $this->setAttribute('longitude', $location->getLongitude());
    }

    public function setImage($filename)
    {
        $this->setAttribute('image', $filename);
    }

    public function getImage()
    {
        return $this->getAttribute('image');
    }

    public function getDetails()
    {
        return $this->getAttribute('details');
    }

    public function setDetails($details)
    {
        $this->setAttribute('details', $details);
    }
}
