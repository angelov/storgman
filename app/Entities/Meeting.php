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

namespace Angelov\Eestec\Platform\Entities;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meetings';

    /**
     * List of members who attended the meeting
     *
     * @var Member[] $attendants
     */
    protected $attendants = [];

    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->getAttribute('date');
    }

    public function setDate(\DateTime $date)
    {
        $this->setAttribute('date', $date);
    }

    public function getLocation()
    {
        return $this->getAttribute('location');
    }

    public function setLocation($location)
    {
        $this->setAttribute('location', $location);
    }

    public function getInfo()
    {
        return $this->getAttribute('info');
    }

    public function setInfo($info)
    {
        $this->setAttribute('info', $info);
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->getAttribute('created_at');
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute('updated_at');
    }

    /**
     * @return Member[]
     */
    public function getAttendants()
    {
        return $this->getAttribute('attendants')->all();
    }

    /**
     * @param Member[] $attendants
     */
    public function addAttendants(array $attendants)
    {
        $this->attendants = array_merge($this->attendants, $attendants);
    }

    /**
     * @param Member[]|int[] $attendants
     */
    public function syncAttendants(array $attendants)
    {
        $ids = [];

        foreach ($attendants as $attendant) {
            if ($attendant instanceof Member) {
                $ids[] = $attendant->getId();
            } elseif (is_int($attendant)) {
                $ids[] = $attendant;
            }
        }

        $this->attendants()->sync($ids);
    }

    public function hasAttendants()
    {
        return count($this->attendants) > 0;
    }

    public function wasAttendedBy(Member $member)
    {
        foreach ($this->getAttendants() as $attendant) {
            if ($attendant->getId() == $member->getId()) {
                return true;
            }
        }

        return false;
    }

    public function setCreator(Member $creator)
    {
        $this->creator()->associate($creator);
    }

    public function getCreator()
    {
        return $this->creator;
    }

    public function attendants()
    {
        return $this->belongsToMany('Angelov\Eestec\Platform\Entities\Member');
    }

    public function creator()
    {
        return $this->belongsTo('Angelov\Eestec\Platform\Entities\Member', 'created_by');
    }

    public function save(array $options = [])
    {
        parent::save($options);

        if (count($this->attendants) > 0) {
            $this->syncAttendants($this->attendants);
        }
    }
}
