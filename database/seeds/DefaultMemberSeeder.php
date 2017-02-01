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

use Illuminate\Database\Seeder;
use Angelov\Eestec\Platform\Membership\Fee;
use Angelov\Eestec\Platform\Members\Member;
use Angelov\Eestec\Platform\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;

class DefaultMemberSeeder extends Seeder
{
    protected $members;
    protected $fees;

    public function __construct(
        MembersRepositoryInterface $members,
        FeesRepositoryInterface $fees
    ) {
        $this->members = $members;
        $this->fees = $fees;
    }

    public function run()
    {
        DB::table('members')->delete();

        $user = new Member();
        $user->setEmail("admin@ultim8.info");
        $user->setPassword(Hash::make('123456'));
        $user->setFirstName("Administrator");
        $user->setLastName("DontNeedIt");

        $user->setFieldOfStudy("Something");
        $user->setYearOfGraduation(2015);

        $user->setBirthday(new \DateTime("1990-01-01"));

        $user->setBoardMember(true);
        $user->setPositionTitle("Administrator");

        $user->setPhoneNumber("+38972000000");
        $user->setWebsite("http://angelovdejan.me");

        $user->setApproved(true);

        $this->members->store($user);

        $fee = new Fee();
        $today = new \DateTime('now');
        $fee->setFromDate($today);
        $fee->setToDate($today->modify("+1 year"));
        $fee->setMember($user);

        $this->fees->store($fee);
    }
}
