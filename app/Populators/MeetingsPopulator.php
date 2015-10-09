<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Eestec\Platform\Populators;

use Angelov\Eestec\Platform\Entities\Meeting;
use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Angelov\Eestec\Platform\Services\MeetingsService;
use Illuminate\Contracts\Auth\Guard;

class MeetingsPopulator
{
    protected $authenticator;
    protected $members;
    protected $meetingsService;

    public function __construct(
        MeetingsService $meetingsService,
        Guard $authenticator,
        MembersRepositoryInterface $members
    ) {
        $this->authenticator = $authenticator;
        $this->members = $members;
        $this->meetingsService = $meetingsService;
    }

    public function populateFromArray(Meeting $meeting, array $data)
    {
        $meeting->setDate(new \DateTime($data['date']));
        $meeting->setLocation($data['location']);
        $meeting->setInfo($data['details']);

        /** @var Member $creator */
        $creator = $this->authenticator->user();
        $meeting->setCreator($creator);

        $attendants = $this->meetingsService->extractAttendants($data['attendants']);

        if ($meeting->hasAttendants()) {
            $meeting->syncAttendants($attendants);
        } else {
            $meeting->addAttendants($attendants);
        }

        return $meeting;
    }
}
