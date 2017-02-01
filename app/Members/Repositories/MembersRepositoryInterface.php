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

namespace Angelov\Storgman\Members\Repositories;

use Angelov\Storgman\Core\Repositories\RepositoryInterface;
use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Core\Exceptions\ResourceNotFoundException;
use Angelov\Storgman\Membership\Reports\MembershipStatusReport;
use Angelov\Storgman\Members\Reports\NewMembersPerMonthReport;

interface MembersRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the member with the given ID
     *
     * @param int $id
     * @return Member
     * @throws ResourceNotFoundException
     */
    public function get($id);

    /**
     * Returns all members
     *
     * @param array $withRelationships
     * @return Member[]
     */
    public function all(array $withRelationships = []);

    /**
     * Stores the given member
     *
     * @param  Member $member
     * @return void
     */
    public function store(Member $member);

    /**
     * Returns the number of total and active members
     *
     * @return MembershipStatusReport
     */
    public function countByMembershipStatus();

    /**
     * Returns the members with birthday on a given date
     *
     * @param DateTime $date
     * @return array
     */
    public function getByBirthdayDate(DateTime $date);

    /**
     * Counts the number of new members per months in a given period
     *
     * @param DateTime $from
     * @param DateTime $to
     * @return NewMembersPerMonthReport
     */
    public function countNewMembersPerMonth(DateTime $from, DateTime $to);

    /**
     * Returns an array with the board members
     *
     * @return Member[]
     */
    public function getBoardMembers();

    /**
     * Returns an array with the unapproved member accounts
     *
     * @return array
     */
    public function getUnapprovedMembers();
}
