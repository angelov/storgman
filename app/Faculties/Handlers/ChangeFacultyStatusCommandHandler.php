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

namespace Angelov\Storgman\Faculties\Handlers;

use Angelov\Storgman\Faculties\Commands\ChangeFacultyStatusCommand;
use Angelov\Storgman\Faculties\Repositories\FacultiesRepositoryInterface;

class ChangeFacultyStatusCommandHandler
{
    protected $faculties;

    public function __construct(FacultiesRepositoryInterface $faculties)
    {
        $this->faculties = $faculties;
    }

    public function handle(ChangeFacultyStatusCommand $command)
    {
        $faculty = $this->faculties->get($command->getFacultyId());

        $faculty->setEnabled($command->isEnabled());

        $this->faculties->store($faculty);
    }
}