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

use Angelov\Eestec\Platform\Model\Meeting;

/**
 * @see https://github.com/JeffreyWay/Laravel-Test-Helpers/issues/6
 */
class MeetingTest extends TestCase
{
    /** @var $entity Meeting */
    protected $entity;

    public function setUp()
    {
        $this->entity = new Meeting();
    }

    public function testHasManyAttendants()
    {
        /** @todo Test this. */
        //$this->assertBelongsToMany('attendants', get_class($this->entity));
    }

    public function testHasOneCreator()
    {
        /** @todo Test this. */
        //$this->assertHasOne('creator', get_class($this->entity));
    }

    public function testReturnsFormattedDate()
    {
        $this->entity->date = '2014-09-07 00:00:00';

        $this->assertEquals('2014-09-07', $this->entity->date);
    }
}