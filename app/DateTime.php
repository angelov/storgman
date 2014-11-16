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

namespace Angelov\Eestec\Platform;

use Carbon\Carbon;

class DateTime extends Carbon
{
    public static function oneYearAgo()
    {
        $date = new self;
        $date->modify('-1 year');

        return $date;
    }

    public static function twelveMonthsAgo($includeCurrent = false)
    {
        $date = new self;
        $date->modify('first day of this month')
             ->modify('-1 year');

        if ($includeCurrent) {
            $date->modify('+1 month');
        }

        return $date;
    }

    public static function nowAsDateString()
    {
        $date = new self;
        return $date->toDateString();
    }

    public static function monthsBetween(DateTime $from, DateTime $to)
    {
        $diff = $from->diff($to);

        return abs($diff->y*12 + $diff->m);
    }
}