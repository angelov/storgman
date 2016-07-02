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

namespace Angelov\Eestec\Platform\Meetings;

use Angelov\Eestec\Platform\Core\DateTime;
use Angelov\Eestec\Platform\Members\Member;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meetings';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTitle('Untitled meeting');
    }

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

    public function getTitle()
    {
        return $this->getAttribute('title');
    }

    public function setTitle($title)
    {
        $this->setAttribute('title', $title);
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

    public function getTime()
    {
        return $this->getDate();
    }

    public function hasPassed()
    {
        return $this->getDate() < (new DateTime());
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
     * @param Member[] $attendants
     */
    public function syncAttendants(array $attendants)
    {
        $ids = [];

        foreach ($attendants as $attendant) {
            if ($attendant instanceof Member) {
                $ids[] = $attendant->getId();
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

    /**
     * @return \Angelov\Eestec\Platform\Members\Member
     */
    public function getCreator()
    {
        return $this->creator;
    }

    public function attendants()
    {
        return $this->belongsToMany('Angelov\Eestec\Platform\Members\Member');
    }

    public function creator()
    {
        return $this->belongsTo('Angelov\Eestec\Platform\Members\Member', 'created_by');
    }

    public function hasReport()
    {
        return $this->attendants()->count() != 0 || $this->getMinutes() != "";
    }

    public function needsReport()
    {
        return $this->hasPassed() && !$this->hasReport();
    }

    public function reportAuthor()
    {
        return $this->belongsTo(Member::class, 'report_author');
    }

    /**
     * @return Member
     */
    public function getReportAuthor()
    {
        return $this->reportAuthor;
    }

    public function setReportAuthor(Member $member)
    {
        $this->reportAuthor()->associate($member);
    }

    public function getMinutes()
    {
        return $this->getAttribute('minutes');
    }

    public function setMinutes($minutes)
    {
        $this->setAttribute('minutes', $minutes);
    }

    public function save(array $options = [])
    {
        parent::save($options);

        if (count($this->attendants) > 0) {
            $this->syncAttendants($this->attendants);
        }
    }
}
