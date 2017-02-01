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

namespace Angelov\Storgman\Members\Http\Controllers;

use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Faculties\Repositories\FacultiesRepositoryInterface;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Angelov\Storgman\Members\MembersStatisticsService;
use Illuminate\Contracts\Auth\Guard;

class HomeController extends BaseController
{
    protected $members;
    protected $meetings;
    protected $faculties;
    protected $membersStats;

    public function __construct(
        MembersRepositoryInterface $members,
        MeetingsRepositoryInterface $meetings,
        FacultiesRepositoryInterface $faculties,
        MembersStatisticsService $membersStats
    ) {
        $this->members = $members;
        $this->meetings = $meetings;
        $this->membersStats = $membersStats;
        $this->faculties = $faculties;
    }

    public function showHomepage(Guard $auth)
    {
        $today = new DateTime();

        /** @var Member $logged */
        $logged = $auth->user();
        $boardMember = $logged->isBoardMember();

        if (!$boardMember) {
            return redirect()->route('members.show', $logged->getId());
        }

        $withBirthday = $this->members->getByBirthdayDate($today);
        $attendance = $this->meetings->calculateAttendanceDetails();
        $byMembershipStatus = $this->members->countByMembershipStatus();
        $perFaculty = json_encode($this->faculties->countPerFaculty());

        $perMonthAll = $this->membersStats->newMembersMonthlyLastYear();

        $perMonth = [
            'months' => json_encode($perMonthAll->getMonthsTitles()),
            'values' => json_encode($perMonthAll->getMonthsValues())
        ];

        $vars = compact('withBirthday', 'attendance', 'byMembershipStatus', 'perFaculty', 'perMonth', 'boardMember');

        return view('homepage.index', $vars);
    }
}
