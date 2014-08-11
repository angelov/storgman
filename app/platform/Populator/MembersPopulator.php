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

namespace Angelov\Eestec\Platform\Populator;

use Angelov\Eestec\Platform\Model\Member;
use Angelov\Eestec\Platform\Repository\PhotosRepositoryInterface;
use App;
use Hash;
use Illuminate\Http\Request;

class MembersPopulator
{
    public function populateFromRequest(Member $member, Request $request)
    {
        $member->first_name = $request->get('first_name');
        $member->last_name = $request->get('last_name');
        $member->birthday = $request->get('birthday');
        $member->email = $request->get('email');

        // Only when creating new member
        if (!isset($member->password)) {
            $member->password = Hash::make('123456'); // Hash::make(str_random(8))
        }

        $member->faculty = $request->get('faculty');
        $member->field_of_study = $request->get('field_of_study');
        $member->board_member = ($request->get('board_member') == 1);
        $member->position_title = $request->get('position_title');

        if ($request->hasFile('member_photo')) {

            $photo = $request->file('member_photo');

            /** @todo This needs to be placed somewhere else */
            /** @var PhotosRepositoryInterface $photos */
            $photos = App::make('PhotosRepository');
            $photoFileName = md5($member->email) . "." . $photo->getClientOriginalExtension();
            $photos->store($photo, 'members', $photoFileName);

            $member->photo = $photoFileName;

        }

        return $member;
    }
}
