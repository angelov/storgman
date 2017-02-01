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

namespace Angelov\Storgman\Members;

use Angelov\Storgman\Faculties\Repositories\FacultiesRepositoryInterface;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Members\Photos\Repositories\PhotosRepositoryInterface;
use DateTime;
use Illuminate\Contracts\Hashing\Hasher;

class MembersPopulator
{
    protected $hasher;
    protected $photos;
    protected $faculties;

    public function __construct(Hasher $hasher, PhotosRepositoryInterface $photos, FacultiesRepositoryInterface $faculties)
    {
        $this->hasher = $hasher;
        $this->photos = $photos;
        $this->faculties = $faculties;
    }

    public function populateFromArray(Member $member, array $data)
    {
        $member->setFirstName($data['first_name']);
        $member->setLastName($data['last_name']);
        $member->setBirthday(new DateTime($data['birthday']));
        $member->setEmail($data['email']);

        if ($data['password']) {
            $hashed = $this->hasher->make($data['password']);
            $member->setPassword($hashed);
        }

        $member->setFieldOfStudy($data['field_of_study']);
        $member->setYearOfGraduation($data['year_of_graduation']);
        $member->setBoardMember(array_get($data, 'board_member', 0) == 1);

        if (isset($data['position_title'])) {
            $member->setPositionTitle($data['position_title']);
        }

        if (isset($data['alumni_member'])) {
            $member->setAlumniMember($data['alumni_member'] == 1);
        }

        if (isset($data['faculty'])) {
            $faculty = $this->faculties->get($data['faculty']);
            $member->setFaculty($faculty);
        }

        $member->setFacebook($data['facebook']);
        $member->setTwitter($data['twitter']);
        $member->setGooglePlus($data['google_plus']);

        $member->setPhoneNumber($data['phone']);
        $member->setWebsite($data['website']);

        return $member;
    }
}
