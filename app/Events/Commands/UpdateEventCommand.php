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

namespace Angelov\Storgman\Events\Commands;

use Angelov\Storgman\Core\Command;
use Angelov\Storgman\Events\EventImage;
use Carbon\Carbon;

class UpdateEventCommand extends Command
{
    protected $id;
    protected $title;
    protected $description;
    protected $hostId;
    protected $url;
    protected $image;
    protected $startDate;
    protected $endDate;
    protected $deadline;

    public function __construct(
        $id,
        $title,
        $description,
        $hostId,
        $url,
        EventImage $image,
        Carbon $startDate,
        Carbon $endDate,
        Carbon $deadline
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->hostId = $hostId;
        $this->url = $url;
        $this->image = $image;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->deadline = $deadline;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getHostId()
    {
        return $this->hostId;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getStartDate()
    {
        return $this->startDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getDeadline()
    {
        return $this->deadline;
    }
}
