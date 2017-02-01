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

namespace Angelov\Storgman\Meetings\Commands;

use Angelov\Storgman\Core\Command;

class CreateMeetingCommand extends Command
{
    protected $title;
    protected $location;
    protected $date;
    protected $details;
    protected $notifyMembers;
    protected $attachments = [];
    protected $creatorId;

    public function __construct($title, $location, $date, $details, $creatorId, array $attachments = [], $notifyMembers = true)
    {
        $this->title = $title;
        $this->location = $location;
        $this->date = $date;
        $this->details = $details;
        $this->notifyMembers = $notifyMembers;
        $this->creatorId = $creatorId;
        $this->attachments = $attachments;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getNotifyMembers()
    {
        return $this->notifyMembers;
    }

    public function getCreatorId()
    {
        return $this->creatorId;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }
}
