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

namespace Angelov\Storgman\Events\Comments\Commands;

use Angelov\Storgman\Core\Command;

class StoreCommentCommand extends Command
{
    protected $eventId;
    protected $authorId;
    protected $content;

    public function __construct( $authorId, $eventId, $content)
    {
        $this->eventId = $eventId;
        $this->authorId = $authorId;
        $this->content = $content;
    }

    public function getEventId()
    {
        return $this->eventId;
    }

    public function getAuthorId()
    {
        return $this->authorId;
    }

    public function getContent()
    {
        return $this->content;
    }
}
