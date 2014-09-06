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

namespace Angelov\Eestec\Platform\Repository;

use Angelov\Eestec\Platform\Exception\NoFeesException;
use Angelov\Eestec\Platform\Model\Fee;
use Angelov\Eestec\Platform\Model\Member;

class EloquentFeesRepository extends AbstractEloquentRepository implements FeesRepositoryInterface
{
    public function __construct(Fee $model)
    {
        $this->model = $model;
    }

    public function store(Fee $fee, Member $member)
    {
        $member->fees()->save($fee);
    }

    public function getFeesForMember(Member $member)
    {
        $fees = $member->fees()->get()->all();

        return $fees;
    }

    public function getLatestFeeForMember(Member $member)
    {
        return $this->getFeeByOrder($member, "DESC");
    }

    public function getFirstFeeForMember(Member $member)
    {
        return $this->getFeeByOrder($member, "ASC");
    }

    private function getFeeByOrder(Member $member, $order)
    {
        $fee = $member->fees()->orderBy('to_date', $order)->first();

        if ($fee == null) {
            throw new NoFeesException();
        }

        return $fee;
    }
}
