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

namespace Angelov\Eestec\Platform\Events\Handlers;

use Angelov\Eestec\Platform\Core\FileSystem\FileSystemsRegistry;
use Angelov\Eestec\Platform\Events\Commands\StoreEventCommand;
use Angelov\Eestec\Platform\Events\Event;
use Angelov\Eestec\Platform\Events\EventImage;
use Angelov\Eestec\Platform\Events\Repositories\EventsRepositoryInterface;
use Angelov\Eestec\Platform\LocalCommittees\Repositories\LocalCommitteesRepositoryInterface;

class StoreEventCommandHandler
{
    protected $events;
    protected $localCommittees;
    protected $fileSystem;

    public function __construct(
        EventsRepositoryInterface $events,
        LocalCommitteesRepositoryInterface $localCommittees,
        FileSystemsRegistry $fileSystemsRegistry
    ) {
        $this->events = $events;
        $this->localCommittees = $localCommittees;
        $this->fileSystem = $fileSystemsRegistry->get(EventImage::class);
    }

    public function handle(StoreEventCommand $command)
    {
        $event = new Event();

        $event->setTitle($command->getTitle());
        $event->setDescription($command->getDescription());

        $host = $this->localCommittees->get($command->getHostId());
        $event->setHost($host);

        $event->setUrl($command->getUrl());

        $file = $this->fileSystem->store($command->getImage());
        $event->setImage($file->getFilename());

        $event->setStartDate($command->getStartDate());
        $event->setEndDate($command->getEndDate());
        $event->setDeadline($command->getDeadline());

        $this->events->store($event);
    }
}
