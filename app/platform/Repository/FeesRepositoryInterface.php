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

interface FeesRepositoryInterface {

    /**
     * @param Fee $fee
     * @param Member $member
     * @return mixed
     */
    public function store(Fee $fee, Member $member);

    /**
     * @param Member $member
     * @return mixed
     * @throws NoFeesException
     */
    public function getFeesForMember(Member $member);

    /**
     * @param Member $member
     * @return mixed
     * @throws NoFeesException
     */
    public function getLatestFeeForMember(Member $member);

    /**
     * @param $id int
     * @return mixed
     */
    public function destroy($id);

}