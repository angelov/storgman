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

namespace Angelov\Eestec\Platform\Reports;

use JsonSerializable;

class ExpectedAndPaidFeesPerMonthReport extends AbstractMonthlyReport implements JsonSerializable
{
    protected $paid;
    protected $expected;

    public function setPaidFees(array $paid)
    {
        $this->paid = $paid;
    }

    public function getPaidFees()
    {
        return $this->paid;
    }

    public function setExpectedFees(array $expected)
    {
        $this->expected = $expected;
    }

    public function getExpectedFees()
    {
        return $this->expected;
    }

    public function jsonSerialize()
    {
        $data = [
            'months' => $this->getMonthsTitles(),
            'paid' => $this->getPaidFees(),
            'expected' => $this->getExpectedFees()
        ];

        return $data;
    }
}
