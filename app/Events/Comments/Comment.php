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

namespace Angelov\Storgman\Events\Comments;

use Angelov\Storgman\Events\Event;
use Angelov\Storgman\Members\Member;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "event_comments";

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function author()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return Member
     */
    public function getAuthor()
    {
        return $this->getAttribute('author');
    }

    public function setAuthor(Member $author)
    {
        $this->author()->associate($author);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function setEvent(Event $event)
    {
        $this->event()->associate($event);
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->getAttribute('event');
    }

    public function getContent()
    {
        return $this->getAttribute('content');
    }

    public function setContent($content)
    {
        $this->setAttribute('content', $content);
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->getAttribute('created_at');
    }
}
