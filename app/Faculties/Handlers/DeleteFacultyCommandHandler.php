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

namespace Angelov\Eestec\Platform\Faculties\Handlers;

use Angelov\Eestec\Platform\Faculties\Commands\DeleteFacultyCommand;
use Angelov\Eestec\Platform\Faculties\Repositories\FacultiesRepositoryInterface;

class DeleteFacultyCommandHandler
{
    protected $faculties;

    public function __construct(FacultiesRepositoryInterface $faculties)
    {
        $this->faculties = $faculties;
    }

    public function handle(DeleteFacultyCommand $command)
    {
        // @todo Check if there are members from the given faculty and throw exception if needed

        $this->faculties->destroy($command->getFacultyId());
    }
}
