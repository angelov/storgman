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

use Angelov\Eestec\Platform\DateTime;
use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $faculty
 * @property string $field_of_study
 * @property string $photo
 * @property string $birthday
 * @property bool $board_member
 * @property string $position_title
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 * @property string $full_name
 * @property string $twitter
 * @property string $facebook
 * @property string $google_plus
 * @property string $phone
 * @property string $website
 * @property bool $approved
 * @property int $year_of_graduation
 * @property int $age
 * @property string $membership_status
 * @property string $membership_expiration_date
 * @property \Illuminate\Database\Eloquent\Collection $fees
 * @property \Illuminate\Database\Eloquent\Collection $meetingsAttended
 * @property \Illuminate\Database\Eloquent\Collection $meetingsCreated
 * @property bool alumni
 */
class Member extends Model implements AuthenticatableInterface, CanResetPasswordInterface
{
    use Authenticatable, CanResetPassword;

    protected $membershipStatus = null;
    protected $membershipExpirationDate = null;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'members';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password', 'remember_token');

    protected $appends = array('full_name', 'membership_status', 'membership_expiration_date');

    /**
     * Membership fees paid by the member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fees()
    {
        return $this->hasMany('Angelov\Eestec\Platform\Entities\Fee');
    }

    public function getId()
    {
        return $this->getAttribute('id');
    }

    /**
     * Concatenate and return the first and last name of the member
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . " " . $this->last_name;
    }

    public function getMembershipStatusAttribute()
    {
        return ($this->membershipStatus) ? "Active" : "Inactive";
    }

    public function setMembershipStatusAttribute($status)
    {
        $this->membershipStatus = $status;
    }

    public function getMembershipExpirationDateAttribute()
    {
        if (!isset($this->membershipExpirationDate)) {
            return "n/a";
        }

        return $this->membershipExpirationDate->toDateString();
    }

    public function getPhotoAttribute($photo)
    {
        if (isset($photo)) {
            return $photo;
        }

        return "default-member-photo.png";
    }

    /**
     * @param DateTime|null $date
     */
    public function setMembershipExpirationDateAttribute(DateTime $date = null)
    {
        $this->membershipExpirationDate = $date;
    }

    public function isBoardMember()
    {
        return $this->board_member;
    }

    public function isAlumniMember()
    {
        return $this->alumni;
    }

    public function getAgeAttribute()
    {
        $birthday = new Carbon($this->birthday);

        return $birthday->age;
    }

    public function meetingsAttended()
    {
        return $this->belongsToMany('Angelov\Eestec\Platform\Entities\Meeting');
    }

    public function meetingsCreated()
    {
        return $this->hasMany('Angelov\Eestec\Platform\Entities\Meeting');
    }

}
