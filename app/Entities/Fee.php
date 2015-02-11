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

namespace Angelov\Eestec\Platform\Entities;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fees';

    /**
     * The member who paid the fee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo('Angelov\Eestec\Platform\Entities\Member');
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * @return DateTime
     */
    public function getFromDate()
    {
        return $this->getAttribute('from_date');
    }

    public function setFromDate(DateTime $date)
    {
        $this->setAttribute('from_date', $date);
    }

    /**
     * @return DateTime
     */
    public function getToDate()
    {
        return $this->getAttribute('to_date');
    }

    public function setToDate(DateTime $date)
    {
        $this->setAttribute('to_date', $date);
    }

    public function setMember(Member $member)
    {
        $this->member()->associate($member);
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->getAttribute('member');
    }
}
