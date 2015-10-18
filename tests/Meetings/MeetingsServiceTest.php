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

namespace Angelov\Eestec\Platform\Tests\Meetings;

use Angelov\Eestec\Platform\Meetings\Meeting;
use Angelov\Eestec\Platform\Meetings\MeetingsService;
use Angelov\Eestec\Platform\Meetings\Reports\MeetingAttendedReport;
use Angelov\Eestec\Platform\Meetings\Reports\MeetingsAttendanceDetailsForMemberReport;
use Angelov\Eestec\Platform\Meetings\Reports\MeetingsAttendedByMemberPerMonthReport;
use Angelov\Eestec\Platform\Meetings\Reports\MeetingsPerMonthReport;
use Angelov\Eestec\Platform\Meetings\Repositories\MeetingsRepositoryInterface;
use Angelov\Eestec\Platform\Members\Member;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Angelov\Eestec\Platform\Membership\MembershipService;
use Angelov\Eestec\Platform\Tests\TestCase;
use Carbon\Carbon;
use Mockery;

class MeetingsServiceTest extends TestCase
{
    /** @var $meetings Mockery\MockInterface */
    protected $meetings;

    /** @var $members Mockery\MockInterface */
    protected $members;

    /** @var $membershipService Mockery\MockInterface */
    protected $membershipService;

    public function setUp()
    {
        parent::setUp();

        $this->meetings = Mockery::mock(MeetingsRepositoryInterface::class);
        $this->members = Mockery::mock(MembersRepositoryInterface::class);
        $this->membershipService = Mockery::mock(MembershipService::class);
    }

    public function testCanCalculateAttendanceDetailsForMember()
    {
        $member = Mockery::mock(Member::class);
        $member->shouldReceive('getJoiningDate')->withNoArgs()
            ->andReturn(new Carbon('1 month ago'));

        $this->meetings->shouldReceive('countAttendanceForMember')->andReturn(5);
        $this->meetings->shouldReceive('countMeetingsInPeriod')->andReturn(10);

        $membershipService = Mockery::mock(MembershipService::class);

        $service = new MeetingsService($this->meetings, $this->members, $membershipService);

        $report = $service->calculateAttendanceDetailsForMember($member);

        $this->assertInstanceOf(MeetingsAttendanceDetailsForMemberReport::class, $report);

        $this->assertEquals(5, $report->getAttended());
        $this->assertEquals(5, $report->getMissed());
        $this->assertEquals(10, $report->getTotal());
        $this->assertEquals(50, $report->getRate());
    }

    public function testCanGetLatestMeetingsAttendanceStatusForMember()
    {
        $member = Mockery::mock(Member::class);
        $meetings = [];

        $numOfMeetings = 10;

        $membershipService = Mockery::mock(MembershipService::class);

        for ($i=0; $i<$numOfMeetings; $i++) {
            $meeting = Mockery::mock(Meeting::class);
            $attendance = ($i < 5) ? true : false;

            $meeting->shouldReceive('wasAttendedBy')->andReturn($attendance);

            $meetings[] = $meeting;
        }

        $this->meetings->shouldReceive('latest')->andReturn($meetings);

        $service = new MeetingsService($this->meetings, $this->members, $membershipService);

        $reports = $service->latestMeetingsAttendanceStatusForMember($member);

        $this->assertTrue(is_array($reports));
        $this->assertEquals(count($reports), 10);

        for ($i=0; $i<$numOfMeetings; $i++) {
            $attendance = ($i < 5) ? true : false;

            $this->assertInstanceOf(MeetingAttendedReport::class, $reports[$i]);
            $this->assertEquals($member, $reports[$i]->getMember());
            $this->assertEquals($meetings[$i], $reports[$i]->getMeeting());

            $this->assertEquals($reports[$i]->getAttended(), $attendance);
        }
    }

    public function testCanCalculateMonthlyAttendanceDetailsForMember()
    {
        $member = Mockery::mock(Member::class);
        $membershipService = Mockery::mock(MembershipService::class);

        $meetingsPerMonthReport = Mockery::mock(MeetingsPerMonthReport::class);

        $monthTitles = ["Jan", "Feb", "Mar"];
        $monthValues = [10, 5, 10];

        $meetingsPerMonthReport->shouldReceive("getMonthsTitles")->andReturn($monthTitles);
        $meetingsPerMonthReport->shouldReceive("getMonthsValues")->andReturn($monthValues);

        $this->meetings->shouldReceive("countMeetingsPerMonth")->andReturn($meetingsPerMonthReport);

        $attendedMeetingsPerMonthReport = Mockery::mock(MeetingsPerMonthReport::class);

        $attendedMeetingsPerMonthReport->shouldReceive("getMonthsValues")->andReturn($monthValues);

        $this->meetings->shouldReceive("countAttendedMeetingsByMemberPerMonth")->andReturn($attendedMeetingsPerMonthReport);

        $service = new MeetingsService($this->meetings, $this->members, $membershipService);

        $report = $service->calculateMonthlyAttendanceDetailsForMember($member);

        $this->assertInstanceOf(MeetingsAttendedByMemberPerMonthReport::class, $report);

        $this->assertEquals($report->getAttended(), $monthValues);
        $this->assertEquals($report->getTotal(), $monthValues);
        $this->assertEquals($report->getMonths(), $monthTitles);
    }

    public function testCanParseValidAttendantsIds()
    {
        $service = new MeetingsService($this->meetings, $this->members, $this->membershipService);

        $attendantIds = "13|14|1|2|3|";

        $parsed = $service->parseAttendantsIds($attendantIds);

        $this->assertEquals([13, 14, 1, 2, 3], $parsed);
    }

    public function testShouldIgnoreInvalidIdsWhenParsingAttendantsIds()
    {
        $service = new MeetingsService($this->meetings, $this->members, $this->membershipService);

        $attendantIds = "asd";

        $parsed = $service->parseAttendantsIds($attendantIds);

        $this->assertEquals([], $parsed);
    }

    public function testCanPrepareAttendantsIds()
    {
        $service = new MeetingsService($this->meetings, $this->members, $this->membershipService);
        $members = [];

        for ($i=0; $i<3; $i++) {
            $member = Mockery::mock(Member::class);
            $member->shouldReceive("getId")->andReturn($i);

            $members[] = $member;
        }

        $str = $service->prepareAttendantsIds($members);

        $this->assertEquals("|0|1|2|", $str);
    }

    public function testCanPrepareOnlyMemberObjects()
    {
        $service = new MeetingsService($this->meetings, $this->members, $this->membershipService);
        $members = [1, 2];

        $this->setExpectedException(\InvalidArgumentException::class);

        $service->prepareAttendantsIds($members);
    }
}