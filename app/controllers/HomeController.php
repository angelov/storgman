<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Angelov\Eestec\Platform\Repository\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;

class HomeController extends BaseController {

    protected $members;
    protected $meetings;

	public function __construct(MembersRepositoryInterface $members,
                                MeetingsRepositoryInterface $meetings) {
        $this->members = $members;
        $this->meetings = $meetings;

        $this->beforeFilter('auth');
    }

	public function showHomepage() {

        $today = new DateTime('now');
        $withBirthday = $this->members->getByBirthdayDate($today);

        $attendance = $this->meetings->calculateAttendanceDetails();

		return View::make('homepage.index', compact('withBirthday', 'attendance'));

	}

}
