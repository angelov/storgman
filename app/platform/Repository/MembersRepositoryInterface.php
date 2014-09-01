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

use Angelov\Eestec\Platform\Exception\ResourceNotFoundException;
use Angelov\Eestec\Platform\Model\Member;
use Angelov\Eestec\Platform\Report\MembershipStatusReport;
use Angelov\Eestec\Platform\Report\MembersPerFacultyReport;
use Angelov\Eestec\Platform\Report\NewMembersPerMonthReport;

interface MembersRepositoryInterface
{
    /**
     * Returns all members
     *
     * @return array
     */
    public function all();

    /**
     * Deletes a specific member from the storage
     *
     * @param $id int
     * @return void
     * @throws ResourceNotFoundException
     */
    public function destroy($id);

    /**
     * Stores the given member
     *
     * @param  Member $member
     * @return void
     */
    public function store(Member $member);

    /**
     * Returns the member with the given ID
     *
     * @param int $id
     * @return Member
     * @throws ResourceNotFoundException
     */
    public function get($id);

    /**
     * Returns the members for a specific page
     *
     * @param int $page
     * @param int $limit
     * @param array $withRelationships
     * @return \stdClass
     */
    public function getByPage($page, $limit, array $withRelationships = []);

    /**
     * Returns the number of total and active members
     *
     * @return MembershipStatusReport
     */
    public function countByMembershipStatus();

    /**
     * Returns the members with birthday on a given date
     *
     * @param \DateTime $date
     * @return array
     */
    public function getByBirthdayDate(\DateTime $date);

    /**
     * Returns the member with the specific IDs
     *
     * @param array $ids
     * @return array
     */
    public function getByIds(array $ids);

    /**
     * Counts the members per faculties
     *
     * @return MembersPerFacultyReport
     */
    public function countPerFaculty();

    /**
     * Counts the number of new members per months in a given period
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return NewMembersPerMonthReport
     */
    public function countNewMembersPerMonth(\DateTime $from, \DateTime $to);

    /**
     * Counts the members
     *
     * @return int
     */
    public function countAll();

}
