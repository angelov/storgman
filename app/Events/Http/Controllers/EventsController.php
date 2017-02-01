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

namespace Angelov\Storgman\Events\Http\Controllers;

use Angelov\Storgman\Core\FileSystem\FileSystemsRegistry;
use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Events\EventImage;
use Angelov\Storgman\Events\Repositories\EventsRepositoryInterface;

class EventsController extends BaseController
{
    protected $events;

    public function __construct(EventsRepositoryInterface $events)
    {
        $this->events = $events;
    }

    public function index()
    {
        $events = $this->events->getUpcoming();

        return view('events.index', compact('events'));
    }

    public function show($id)
    {
        $event = $this->events->get($id);
        $upcoming = $this->events->getUpcoming();

        return view('events.show', compact('event', 'upcoming'));
    }

    public function image($id, FileSystemsRegistry $fileSystemsRegistry)
    {
        $fileSystem = $fileSystemsRegistry->get(EventImage::class);
        $event = $this->events->get($id);
        $image = $event->getImage();

        $image = $fileSystem->find($image);
        $content = $fileSystem->read($image);

        return response($content)->header('Cache-Control', 'public, max-age=253938');
    }
}