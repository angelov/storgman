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

use Angelov\Eestec\Platform\Exception\MemberNotFoundException;
use Angelov\Eestec\Platform\Model\Member;

interface MembersRepositoryInterface {

    /**
     * @return array
     */
    public function all();

    /**
     * @param $id int
     * @return void
     * @throws MemberNotFoundException
     */
    public function destroy($id);

    /**
     * @param \Angelov\Eestec\Platform\Model\Member $member
     * @return void
     */
    public function store(Member $member);

    /**
     * @param $id
     * @return \Angelov\Eestec\Platform\Model\Member
     * @throws MemberNotFoundException
     */
    public function get($id);

    public function getByPage($page, $limit);

    public function countByMembershipStatus();

    public function getByBirthdayDate(\DateTime $date);

    public function getByIds(array $ids);

}