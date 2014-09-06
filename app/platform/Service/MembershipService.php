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

namespace Angelov\Eestec\Platform\Service;

use Angelov\Eestec\Platform\DateTime;
use Angelov\Eestec\Platform\Exception\NoFeesException;
use Angelov\Eestec\Platform\Model\Member;
use Angelov\Eestec\Platform\Repository\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;

class MembershipService
{

    protected $members;
    protected $fees;

    public function __construct(MembersRepositoryInterface $members, FeesRepositoryInterface $fees)
    {
        $this->members = $members;
        $this->fees = $fees;
    }

    /**
     * Check if the member is active/inactive
     *
     * @param  Member $member
     * @return bool
     */
    public function isMemberActive(Member $member)
    {
        $expirationDate = $this->getExpirationDate($member);

        if ($expirationDate == null) {
            return false;
        }

        $today = new DateTime();

        return $today < $expirationDate;
    }

    /**
     * Get the membership expiration date for a given member
     *
     * @param  \Angelov\Eestec\Platform\Model\Member $member
     * @return DateTime
     */
    public function getExpirationDate(Member $member)
    {
        try {
            $fee = $this->fees->getLatestFeeForMember($member);

            return new DateTime($fee->to_date);
        } catch (NoFeesException $e) {
            return null;
        }
    }

    public function getJoinedDate(Member $member)
    {
        try {
            $fee = $this->fees->getFirstFeeForMember($member);

            return new DateTime($fee->from_date);
        } catch (NoFeesException $e) {
            return new DateTime($member->created_at);
        }
    }

}
