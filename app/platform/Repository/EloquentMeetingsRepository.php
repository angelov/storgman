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

namespace Angelov\Eestec\Platform\Repository;

use Angelov\Eestec\Platform\Exception\MeetingNotFoundException;
use Angelov\Eestec\Platform\Model\Meeting;
use Angelov\Eestec\Platform\Model\Member;
use DB;

class EloquentMeetingsRepository implements MeetingsRepositoryInterface
{

    public function store(Meeting $meeting, Member $creator, array $attendants)
    {
        $meeting->created_by = $creator->id;
        $meeting->save();
        $meeting->attendants()->saveMany($attendants);
    }

    public function all(array $withRelationships = [])
    {
        return Meeting::with($withRelationships)->get()->all();

    }

    public function get($id)
    {
        $meeting = Meeting::find($id);

        if ($meeting == null) {
            throw new MeetingNotFoundException();
        }

        return $meeting;
    }

    public function calculateAttendanceDetails()
    {

        $result = (array) DB::select(
            '
                        select total_meetings as meetings,
                               total_attendants as attendants,
                               round(total_attendants/total_meetings) as average
                        from (
                            (select sum(attendants) as total_attendants
                             from

                                (select meeting_id as meeting,
                                       count(member_id) as attendants
                                 from meeting_member
                                 group by (meeting_id)) as details) as tbl1,

                                (select count(id) as total_meetings
                                 from meetings) as tbl2
                        )
                    '
        )[0];

        $att['meetings'] = $result['meetings'] ? : 0;
        $att['attendants'] = $result['attendants'] ? : 0;
        $att['average'] = $result['average'] ? : 0;

        return $att;

    }

}
