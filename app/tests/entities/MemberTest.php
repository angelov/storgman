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

use Angelov\Eestec\Platform\Model\Member;

/**
 * @see https://github.com/JeffreyWay/Laravel-Test-Helpers/issues/6
 */
class MemberTest extends TestCase
{
    /** @var $entity Member */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Member();
    }

    public function testHasManyFees()
    {
        $this->assertHasMany('fees', get_class($this->entity));
    }

    public function testReturnsFullName()
    {
        $this->entity->first_name = "Dejan";
        $this->entity->last_name = "Angelov";

        $this->assertEquals("Dejan Angelov", $this->entity->full_name);
    }

    public function testReturnsInactiveMembershipStatus()
    {
        $this->entity->membershipStatus = false;

        $this->assertEquals($this->entity->membershipStatus, "Inactive");
    }

    public function testReturnsActiveMembershipStatus()
    {
        $this->entity->membershipStatus = true;

        $this->assertEquals($this->entity->membershipStatus, "Active");
    }

    public function testReturnsValidMembershipExpirationDate()
    {
        $date = Mockery::mock('Angelov\Eestec\Platform\DateTime');
        $date->shouldReceive('toDateString')
            ->once()
            ->andReturn('2014-09-07');

        $this->entity->membershipExpirationDate = $date;

        $this->assertEquals('2014-09-07', $this->entity->membershipExpirationDate);
    }

    public function testReturnsInvalidMembershipExpirationDate()
    {
        $this->entity->membershipExpirationDate = null;

        $this->assertEquals('n/a', $this->entity->membershipExpirationDate);
    }

    public function testMemberHasPhoto()
    {
        $this->entity->photo = 'photo.png';

        $this->assertEquals('photo.png', $this->entity->photo);
    }

    public function testMemberHasNotPhoto()
    {
        $this->entity->photo = null;

        $this->assertEquals('default-member-photo.png', $this->entity->photo);
    }

    public function testMemberIsBoardMember()
    {
        $this->entity->board_member = true;

        $this->assertTrue($this->entity->isBoardMember());
    }

    public function testMemberAttendedManyMeetings()
    {
        /** @todo Test this. */
        //$this->assertBelongsToMany('meetingsAttended', get_class($this->entity));

    }

    public function testMemberCreatedManyMeetings()
    {
        /** @todo Test this. */
        //$this->assertHasMany('meetingsCreated', get_class($this->entity));
    }

}