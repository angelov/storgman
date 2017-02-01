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

namespace Angelov\Storgman\Members\SocialProfiles;

use Angelov\Storgman\Members\Member;
use Illuminate\Database\Eloquent\Model;

class SocialProfile extends Model
{
    protected $table = "social_profiles";

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * @return Member
     */
    public function getMember()
    {
        return $this->member;
    }

    public function setMember(Member $member)
    {
        $this->member()->associate($member);
    }

    public function getProvider()
    {
        return $this->getAttribute('provider');
    }

    public function setProvider($provider)
    {
        $this->setAttribute('provider', $provider);
    }

    public function getProfileId()
    {
        return $this->getAttribute('profile_id');
    }

    public function setProfileId($profileId)
    {
        $this->setAttribute('profile_id', $profileId);
    }
}
