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

namespace Angelov\Eestec\Platform\Events;

use Angelov\Eestec\Platform\LocalCommittees\LocalCommittee;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = "eestec_events";
    protected $dates = ['start_date', 'end_date', 'deadline'];

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

    public function getDescription()
    {
        return $this->getAttribute('description');
    }

    public function setDescription($desc)
    {
        $this->setAttribute('description', $desc);
    }

    public function host()
    {
        return $this->belongsTo(LocalCommittee::class, 'host_id');
    }

    public function setHost(LocalCommittee $localCommittee)
    {
        $this->host()->associate($localCommittee);
    }

    /**
     * @return LocalCommittee
     */
    public function getHost()
    {
        return $this->getAttribute('host');
    }

    /**
     * @return Carbon
     */
    public function getStartDate()
    {
        return $this->getAttribute('start_date');
    }

    public function setStartDate(Carbon $date)
    {
        $this->setAttribute('start_date', $date);
    }

    /**
     * @return Carbon
     */
    public function getEndDate()
    {
        return $this->getAttribute('end_date');
    }

    public function setEndDate(Carbon $date)
    {
        $this->setAttribute('end_date', $date);
    }

    /**
     * @return Carbon
     */
    public function getDeadline()
    {
        return $this->getAttribute('deadline');
    }

    public function setDeadline(Carbon $date)
    {
        $this->setAttribute('deadline', $date);
    }

    public function getUrl()
    {
        return $this->getAttribute('url');
    }

    public function setUrl($url)
    {
        $this->setAttribute('url', $url);
    }

    public function getImage()
    {
        return $this->getAttribute('image');
    }

    public function setImage($filename)
    {
        $this->setAttribute('image', $filename);
    }
}
