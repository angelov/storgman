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

namespace Angelov\Eestec\Platform\Repositories;

use Angelov\Eestec\Platform\DateTime;
use Angelov\Eestec\Platform\Exceptions\NoFeesException;
use Angelov\Eestec\Platform\Entities\Fee;
use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Reports\ExpectedFeesPerMonthReport;
use Angelov\Eestec\Platform\Reports\PaidFeesPerMonthReport;

interface FeesRepositoryInterface extends RepositoryInterface
{
    /**
     * Stores a fee and relate it to a specific member
     *
     * @param  Fee $fee
     * @param  Member $member
     * @return void
     */
    public function store(Fee $fee, Member $member);

    /**
     * Returns all fees paid by a member
     *
     * @param  Member $member
     * @return array
     * @throws NoFeesException
     */
    public function getFeesForMember(Member $member);

    /**
     * Returns the latest fee paid by a member
     *
     * @param  Member $member
     * @return Fee
     * @throws NoFeesException
     */
    public function getLatestFeeForMember(Member $member);

    /**
     * Returns the first fee paid by a member
     *
     * @param Member $member
     * @return Fee
     * @throws NoFeesException
     */
    public function getFirstFeeForMember(Member $member);

    /**
     * Returns the member who paid the fee
     *
     * @param Fee $fee
     * @return Member
     */
    public function getFeeMember(Fee $fee);

    /**
     * Return the N fees which will expire first
     *
     * @param int $count
     * @return array
     */
    public function getSoonToExpire($count = 10);

    /**
     * Returns a report how many fees have been paid per month
     *
     * @param DateTime $from
     * @param DateTime $to
     * @return ExpectedFeesPerMonthReport
     */
    public function calculateExpectedFeesPerMonth(DateTime $from, DateTime $to);

    /**
     * Returns a report how many fees were expected to be paid per month
     *
     * @param DateTime $from
     * @param DateTime $to
     * @return PaidFeesPerMonthReport
     */
    public function calculatePaidFeesPerMonth(DateTime $from, DateTime $to);
}