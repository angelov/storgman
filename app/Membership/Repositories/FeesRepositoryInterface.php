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

namespace Angelov\Eestec\Platform\Membership\Repositories;

use Angelov\Eestec\Platform\Core\Repositories\RepositoryInterface;
use Angelov\Eestec\Platform\Core\DateTime;
use Angelov\Eestec\Platform\Membership\Fee;
use Angelov\Eestec\Platform\Core\Exceptions\ResourceNotFoundException;
use Angelov\Eestec\Platform\Membership\Reports\ExpectedFeesPerMonthReport;
use Angelov\Eestec\Platform\Membership\Reports\PaidFeesPerMonthReport;

interface FeesRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the member with the given ID
     *
     * @param int $id
     * @return Fee
     * @throws \Angelov\Eestec\Platform\Core\Exceptions\ResourceNotFoundException
     */
    public function get($id);

    /**
     * Stores a fee
     *
     * @param  Fee $fee
     * @return void
     */
    public function store(Fee $fee);

    /**
     * Return the N fees which will expire first
     *
     * @param int $count
     * @return Fee[]
     */
    public function getSoonToExpire($count = 10);

    /**
     * Returns a report how many fees have been paid per month
     *
     * @param DateTime $from
     * @param DateTime $to
     * @return \Angelov\Eestec\Platform\Membership\Reports\ExpectedFeesPerMonthReport
     */
    public function calculateExpectedFeesPerMonth(DateTime $from, DateTime $to);

    /**
     * Returns a report how many fees were expected to be paid per month
     *
     * @param \Angelov\Eestec\Platform\Core\DateTime $from
     * @param \Angelov\Eestec\Platform\Core\DateTime $to
     * @return \Angelov\Eestec\Platform\Membership\Reports\PaidFeesPerMonthReport
     */
    public function calculatePaidFeesPerMonth(DateTime $from, DateTime $to);
}
