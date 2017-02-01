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

namespace Angelov\Storgman\Events\Handlers;

use Angelov\Storgman\Core\FileSystem\FileSystemsRegistry;
use Angelov\Storgman\Events\Commands\UpdateEventCommand;
use Angelov\Storgman\Events\EventImage;
use Angelov\Storgman\Events\Repositories\EventsRepositoryInterface;
use Angelov\Storgman\LocalCommittees\Repositories\LocalCommitteesRepositoryInterface;

class UpdateEventCommandHandler
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

    public function handle(UpdateEventCommand $command)
    {
        $event = $this->events->get($command->getId());

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
