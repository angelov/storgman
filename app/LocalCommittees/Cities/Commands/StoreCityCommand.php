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

namespace Angelov\Storgman\LocalCommittees\Cities\Commands;

use Angelov\Storgman\Core\Command;
use Angelov\Storgman\LocalCommittees\Cities\Location;
use Symfony\Component\HttpFoundation\File\File;

class StoreCityCommand extends Command
{
    protected $name;
    protected $country;
    protected $location;
    protected $image;
    protected $details;

    public function __construct($name, $country, Location $location, File $image, $details)
    {
        $this->name = $name;
        $this->country = $country;
        $this->location = $location;
        $this->image = $image;
        $this->details = $details;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getDetails()
    {
        return $this->details;
    }
}
