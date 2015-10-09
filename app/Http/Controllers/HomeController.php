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

namespace Angelov\Eestec\Platform\Http\Controllers;

use Angelov\Eestec\Platform\DateTime;
use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Repositories\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;
use Angelov\Eestec\Platform\Services\MembersStatisticsService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\Factory;

class HomeController extends BaseController
{
    protected $members;
    protected $meetings;
    protected $membersStats;
    protected $authenticator;
    protected $view;

    public function __construct(
        Guard $authenticator,
        Factory $view,
        MembersRepositoryInterface $members,
        MeetingsRepositoryInterface $meetings,
        MembersStatisticsService $membersStats
    ) {
        $this->members = $members;
        $this->meetings = $meetings;
        $this->membersStats = $membersStats;
        $this->authenticator = $authenticator;
        $this->view = $view;
    }

    public function showHomepage()
    {
        $today = new DateTime();

        /** @var Member $logged */
        $logged = $this->authenticator->user();
        $boardMember = $logged->isBoardMember();

        if (!$boardMember) {
            return \Redirect::route('members.show', $logged->getId());
        }

        $withBirthday = $this->members->getByBirthdayDate($today);
        $attendance = $this->meetings->calculateAttendanceDetails();
        $byMembershipStatus = $this->members->countByMembershipStatus();
        $perFaculty = json_encode($this->members->countPerFaculty());

        $perMonthAll = $this->membersStats->newMembersMonthlyLastYear();

        $perMonth = [
            'months' => json_encode($perMonthAll->getMonthsTitles()),
            'values' => json_encode($perMonthAll->getMonthsValues())
        ];

        return $this->view->make(
            'homepage.index',
            compact('withBirthday', 'attendance', 'byMembershipStatus',
                    'perFaculty', 'perMonth', 'boardMember')
        );
    }
}
