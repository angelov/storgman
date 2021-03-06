<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Illuminate\Database\Seeder;
use Angelov\Storgman\Membership\Fee;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;

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
