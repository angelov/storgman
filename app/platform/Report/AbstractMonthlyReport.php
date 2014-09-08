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

namespace Angelov\Eestec\Platform\Report;

use Angelov\Eestec\Platform\DateTime;
use JsonSerializable;

abstract class AbstractMonthlyReport implements JsonSerializable
{
    protected $months;
    protected $beginDate;
    protected $endDate;

    public function __construct(DateTime $beginDate, DateTime $endDate)
    {
        $this->beginDate = $beginDate;
        $this->endDate = $endDate;

        $this->initializeMonthsArray();
    }

    /**
     * @param mixed $defaultValue
     * @todo Smelly code, smelly code... what are you doing here?
     */
    protected function initializeMonthsArray($defaultValue = 0)
    {
        $year = $this->beginDate->format("Y");
        $count = 0;
        $numNeededMonths = $this->calculateNumberOfMonths();

        for ($m = $this->beginDate->format('m'); $m <= 12; $m++) {

            $count++;

            $dt = new DateTime("1-" . $m . "-" . $year);
            $month = $dt->format('m');
            $this->months[$year . "-" . $month] = $defaultValue;

            if ($m == 12 && $count < 12) {
                $m = 0;
                $year++;
            }

            if ($count == $numNeededMonths) {
                break;
            }

        }
    }

    public function addMonth($month, $value)
    {
        $this->months[$month] = $value;
    }

    private function calculateNumberOfMonths()
    {
        return DateTime::monthsBetween($this->beginDate, $this->endDate) + 1;
    }

    /** @todo There's probably a better way to do this. */
    public function getMonthsTitles()
    {
        $months = array_keys($this->months);

        foreach ($months as &$month) {
            $parts = explode("-", $month);
            $m = $parts[1];
            $y = $parts[0];

            $m = new DateTime("01-" . $m . "-" . $y);

            $month = $m->format("M Y");
        }

        return $months;
    }

    public function getMonthsValues()
    {
        return array_values($this->months);
    }

    public function getMonths()
    {
        return $this->months;
    }

    public function jsonSerialize()
    {
        // @todo
    }

}