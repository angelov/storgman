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

use Illuminate\Database\Seeder;
use Angelov\Eestec\Platform\Entity\Fee;
use Angelov\Eestec\Platform\Entity\Member;
use Angelov\Eestec\Platform\Repository\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;

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
        $user->email = "admin@ultim8.info";
        $user->password = Hash::make('123456');
        $user->first_name = "Administrator";
        $user->last_name = "DontNeedIt";

        $user->faculty = "WillChangeIt";
        $user->field_of_study = "Something";
        $user->year_of_graduation = 2015;

        $user->birthday = "1990-01-01";

        $user->board_member = true;
        $user->position_title = "Administrator";

        $user->phone = "38972000000";
        $user->website = "http://ultim8.info";

        $this->members->store($user);

        $fee = new Fee();
        $today = new \DateTime('now');
        $fee->from_date = $today->format('Y-m-d');
        $fee->to_date = $today->modify("+1 year");

        $this->fees->store($fee, $user);
    }

}
