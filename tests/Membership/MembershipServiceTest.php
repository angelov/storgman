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

namespace Angelov\Storgman\Tests\Meetings;

use Angelov\Storgman\Core\DateTime;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Members\Repositories\MembersRepositoryInterface;
use Angelov\Storgman\Membership\MembershipService;
use Angelov\Storgman\Membership\Reports\ExpectedAndPaidFeesPerMonthReport;
use Angelov\Storgman\Membership\Reports\ExpectedFeesPerMonthReport;
use Angelov\Storgman\Membership\Reports\PaidFeesPerMonthReport;
use Angelov\Storgman\Membership\Repositories\FeesRepositoryInterface;
use Angelov\Storgman\Tests\TestCase;
use Carbon\Carbon;
use Mockery;

class MembershipServiceTest extends TestCase
{
    /** @var $members Mockery\MockInterface */
    protected $members;

    /** @var $fees Mockery\MockInterface */
    protected $fees;

    public function setUp()
    {
        parent::setUp();

        $this->members = Mockery::mock(MembersRepositoryInterface::class);
        $this->fees = Mockery::mock(FeesRepositoryInterface::class);
    }

    public function testCanGenerateExpectedAndPaidFeesPerMonthLastYearReport()
    {
        $membershipService = new MembershipService($this->members, $this->fees);

        $expectedFeesPerMonthReport = Mockery::mock(ExpectedFeesPerMonthReport::class);
        $paidFeesPerMonthReport = Mockery::mock(PaidFeesPerMonthReport::class);

        $this->fees->shouldReceive('calculateExpectedFeesPerMonth')->andReturn($expectedFeesPerMonthReport);
        $this->fees->shouldReceive('calculatePaidFeesPerMonth')->andReturn($paidFeesPerMonthReport);

        $expected = [10, 15, 20];
        $paid = [5, 20, 15];

        $expectedFeesPerMonthReport->shouldReceive('getMonthsValues')->andReturn($expected);
        $paidFeesPerMonthReport->shouldReceive('getMonthsValues')->andReturn($paid);

        $this->fees->shouldReceive('calculateExpectedFeesPerMonth');
        $this->fees->shouldReceive('calculatePaidFeesPerMonth');

        $report = $membershipService->getExpectedAndPaidFeesPerMonthLastYear();

        $this->assertInstanceOf(ExpectedAndPaidFeesPerMonthReport::class, $report);

        $this->assertEquals($paid, $report->getPaidFees());
        $this->assertEquals($expected, $report->getExpectedFees());
    }

    public function testCanGenerateSuggestionDatesForExistingMember()
    {
        $member = Mockery::mock(Member::class);
        $expirationDate = new Carbon('2015-10-22');
        $member->shouldReceive('getExpirationDate')->andReturn($expirationDate);

        $membershipService = new MembershipService($this->members, $this->fees);

        $suggestedDates = $membershipService->suggestDates($member);

        $this->assertTrue(is_array($suggestedDates));
        $this->assertEquals('2015-10-23', $suggestedDates['from']);
        $this->assertEquals('2016-10-23', $suggestedDates['to']);
    }

    public function testCanGenerateSuggestionDatesForNewMember()
    {
        $member = Mockery::mock(Member::class);
        $member->shouldReceive('getExpirationDate')->andReturnNull();

        $membershipService = new MembershipService($this->members, $this->fees);

        $suggestedDates = $membershipService->suggestDates($member);

        $now = new Carbon();
        $nextYear = (new Carbon())->addYear();

        $this->assertTrue(is_array($suggestedDates));
        $this->assertEquals($now->format('Y-m-d'), $suggestedDates['from']);
        $this->assertEquals($nextYear->format('Y-m-d'), $suggestedDates['to']);
    }
}
