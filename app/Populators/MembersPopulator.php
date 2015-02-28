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

namespace Angelov\Eestec\Platform\Populators;

use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Http\Requests\StoreMemberRequest;
use Angelov\Eestec\Platform\Repositories\PhotosRepositoryInterface;
use DateTime;
use Illuminate\Contracts\Hashing\Hasher;

class MembersPopulator
{
    protected $hasher;
    protected $photos;

    public function __construct(Hasher $hasher, PhotosRepositoryInterface $photos)
    {
        $this->hasher = $hasher;
        $this->photos = $photos;
    }

    public function populateFromRequest(Member $member, StoreMemberRequest $request)
    {
        $member->setFirstName($request->get('first_name'));
        $member->setLastName($request->get('last_name'));
        $member->setBirthday(new DateTime($request->get('birthday')));
        $member->setEmail($request->get('email'));

        if ($request->has('password')) {
            $member->setPassword($this->hasher->make($request->get('password')));
        }

        $member->setFaculty($request->get('faculty'));
        $member->setFieldOfStudy($request->get('field_of_study'));
        $member->setYearOfGraduation($request->get('year_of_graduation'));
        $member->setBoardMember($request->get('board_member') == 1);
        $member->setPositionTitle($request->get('position_title'));
        $member->setAlumniMember($request->get('alumni_member') == 1);

        $member->setFacebook($request->get('facebook'));
        $member->setTwitter($request->get('twitter'));
        $member->setGooglePlus($request->get('google_plus'));

        $member->setPhoneNumber($request->get('phone'));
        $member->setWebsite($request->get('website'));

        if ($request->hasFile('member_photo')) {
            $photo = $request->file('member_photo');

            /** @todo This needs to be placed somewhere else */
            $photoFileName = md5($member->email) . "." . $photo->getClientOriginalExtension();
            $this->photos->store($photo, 'members', $photoFileName);

            $member->setPhoto($photoFileName);
        }

        return $member;
    }
}
