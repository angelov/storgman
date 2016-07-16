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

namespace Angelov\Eestec\Platform\Events\Http\Controllers;

use Angelov\Eestec\Platform\Core\FileSystem\FileSystemsRegistry;
use Angelov\Eestec\Platform\Core\Http\Controllers\BaseController;
use Angelov\Eestec\Platform\Events\EventImage;
use Angelov\Eestec\Platform\Events\Repositories\EventsRepositoryInterface;

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

        return view('events.show', compact('event'));
    }

    public function image($id, FileSystemsRegistry $fileSystemsRegistry)
    {
        $fileSystem = $fileSystemsRegistry->get(EventImage::class);
        $event = $this->events->get($id);
        $image = $event->getImage();

        $image = $fileSystem->find($image);
        $content = $fileSystem->read($image);

        return response($content);//->header('Content-Disposition', sprintf('attachment;filename="%s"', $image->getFilename()));
    }
}