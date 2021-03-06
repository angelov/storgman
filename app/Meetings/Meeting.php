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

namespace Angelov\Storgman\Meetings;

use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Meetings\Attachments\Attachment;
use Angelov\Storgman\Members\Member;
use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $table = 'meetings';
    protected $dates = ["date"];

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

    /**
     * @var Attachment[] $attachments
     */
    protected $attachments = [];

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
     * @return Member
     */
    public function getCreator()
    {
        return $this->creator;
    }

    public function attendants()
    {
        return $this->belongsToMany(Member::class);
    }

    public function creator()
    {
        return $this->belongsTo(Member::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'meeting_id');
    }

    public function hasAttachments()
    {
        return count($this->getAttachments()) > 0;
    }

    /**
     * @param Attachment[] $attachments
     */
    public function addAttachments(array $attachments)
    {
        $this->attachments = array_merge($this->attachments, $attachments);
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments()
    {
        return $this->getAttribute('attachments')->all();
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

        if (count($this->attachments) > 0) {
            $this->attachments()->saveMany($this->attachments);
        }
    }
}
