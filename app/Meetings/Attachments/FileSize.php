<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 *
 * This file is part of Storgman.
 *
 * Storgman is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Storgman is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Storgman.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Storgman
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Meetings\Attachments;

use InvalidArgumentException;

class FileSize
{
    const UNIT_BYTE     =  'B';
    const UNIT_KILOBYTE = 'KB';
    const UNIT_MEGABYTE = 'MB';
    const UNIT_GIGABYTE = 'GB';

    protected $value;

    private $rates = [
        self::UNIT_BYTE     => 1000,
        self::UNIT_KILOBYTE =>    1,
        self::UNIT_MEGABYTE =>    0.001,
        self::UNIT_GIGABYTE =>    0.000001
    ];

    public function __construct($value, $unit = self::UNIT_KILOBYTE)
    {
        if (!isset($this->rates[$unit])) {
            throw new InvalidArgumentException("Unit not supported.");
        }

        $this->value = (float) $value;

        if ($unit != self::UNIT_KILOBYTE) {
            $this->value *= $this->rates[$unit];
        }
    }

    public function getValue()
    {
        return $this->value;
    }

    private function convertValueToUnit($unit)
    {
        if (!isset($this->rates[$unit])) {
            throw new InvalidArgumentException("Unit not supported.");
        }

        return $this->value * $this->rates[$unit];
    }

    public function __toString()
    {
        $b = $this->convertValueToUnit(self::UNIT_BYTE);
        $base = log($b, 1000);

        $units = [self::UNIT_BYTE, self::UNIT_KILOBYTE, self::UNIT_MEGABYTE, self::UNIT_GIGABYTE];

        return round(pow(1000, $base - floor($base)), 2) .' '. $units[(int) floor($base)];
    }
}