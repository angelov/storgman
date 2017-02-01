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

namespace Angelov\Storgman\Faculties\Commands;

use Angelov\Storgman\Core\Command;

class ChangeFacultyStatusCommand extends Command
{
    const STATUS_ENABLED = true;
    const STATUS_DISABLED = false;

    protected $facultyId;
    protected $enabled;

    public function __construct($facultyId, $status)
    {
        $this->facultyId = $facultyId;
        $this->enabled = $status === self::STATUS_ENABLED;
    }

    public function getFacultyId()
    {
        return $this->facultyId;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}
