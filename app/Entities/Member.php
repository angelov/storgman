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

use Carbon\Carbon;
use DateTime;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableInterface;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordInterface;
use Illuminate\Database\Eloquent\Model;

class Member extends Model implements AuthenticatableInterface, CanResetPasswordInterface
{
    use Authenticatable, CanResetPassword;

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
    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['full_name', 'membership_status', 'membership_expiration_date'];

    protected $dates = ['birthday'];

    public function getId()
    {
        return $this->getAttribute('id');
    }

    public function getEmail()
    {
        return $this->getAttribute('email');
    }

    public function setEmail($email)
    {
        $this->setAttribute('email', $email);
    }

    public function getPassword()
    {
        return $this->getAttribute('password');
    }

    public function setPassword($password)
    {
        $this->setAttribute('password', $password);
    }

    public function getFirstName()
    {
        return $this->getAttribute('first_name');
    }

    public function setFirstName($firstName)
    {
        $this->setAttribute('first_name', $firstName);
    }

    public function getLastName()
    {
        return $this->getAttribute('last_name');
    }

    public function setLastName($lastName)
    {
        $this->setAttribute('last_name', $lastName);
    }

    public function getFullName()
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    public function getFaculty()
    {
        return $this->getAttribute('faculty');
    }

    public function setFaculty($faculty)
    {
        $this->setAttribute('faculty', $faculty);
    }

    public function getFieldOfStudy()
    {
        return $this->getAttribute('field_of_study');
    }

    public function setFieldOfStudy($field)
    {
        $this->setAttribute('field_of_study', $field);
    }

    public function getYearOfGraduation()
    {
        return $this->getAttribute('year_of_graduation');
    }

    public function setYearOfGraduation($year)
    {
        $this->setAttribute('year_of_graduation', $year);
    }

    public function getPhoto()
    {
        $photo = $this->getAttribute('photo');

        return ($photo) ? $photo : "default-member-photo.png";
    }

    public function setPhoto($photoFileName)
    {
        $this->setAttribute('photo', $photoFileName);
    }

    /**
     * @return Carbon
     */
    public function getBirthday()
    {
        return $this->getAttribute('birthday');
    }

    public function setBirthday(DateTime $birthday)
    {
        $this->setAttribute('birthday', $birthday);
    }

    public function getAge()
    {
        return $this->getBirthday()->age;
    }

    public function isBoardMember()
    {
        return $this->getAttribute('board_member');
    }

    public function setBoardMember($isBoardMember)
    {
        $this->setAttribute('board_member', $isBoardMember);
    }

    public function getPositionTitle()
    {
        return $this->getAttribute('position_title');
    }

    public function setPositionTitle($title)
    {
        $this->setAttribute('position_title', $title);
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->getAttribute('created_at');
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt()
    {
        return $this->getAttribute('updated_at');
    }

    public function getFacebook()
    {
        return $this->getAttribute('facebook');
    }

    public function setFacebook($profile)
    {
        $this->setAttribute('facebook', $profile);
    }

    public function getTwitter()
    {
        return $this->getAttribute('twitter');
    }

    public function setTwitter($profile)
    {
        $this->setAttribute('twitter', $profile);
    }

    public function getGooglePlus()
    {
        return $this->getAttribute('google_plus');
    }

    public function setGooglePlus($profile)
    {
        $this->setAttribute('google_plus', $profile);
    }

    public function getPhoneNumber()
    {
        return $this->getAttribute('phone');
    }

    public function setPhoneNumber($number)
    {
        $this->setAttribute('phone', $number);
    }

    public function getWebsite()
    {
        return $this->getAttribute('website');
    }

    public function setWebsite($url)
    {
        $this->setAttribute('website', $url);
    }

    public function isAlumniMember()
    {
        return $this->getAttribute('alumni');
    }

    public function setAlumniMember($isAlumni)
    {
        $this->setAttribute('alumni', $isAlumni);
    }

    public function isApproved()
    {
        return $this->getAttribute('approved');
    }

    public function setApproved($isApproved)
    {
        $this->setAttribute('approved', $isApproved);
    }

    /**
     * Membership fees paid by the member
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function fees()
    {
        return $this->hasMany('Angelov\Eestec\Platform\Entities\Fee');
    }

    /**
     * @return Fee[]
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @return DateTime
     */
    public function getExpirationDate()
    {
        $fee = $this->getLatestFee();

        return (!$fee) ? null : new DateTime($fee->getToDate());
    }

    /**
     * @return DateTime
     */
    public function getJoiningDate()
    {
        $fee = $this->getFirstFee();

        return ($fee) ? new DateTime($fee->getFromDate()) : $this->getCreatedAt();
    }

    /**
     * @param string $order ASC or DESC
     * @return Fee
     */
    private function getFeeByOrder($order)
    {
        $fee = $this->fees()->orderBy('to_date', $order)->first();

        return $fee;
    }

    public function getLatestFee()
    {
        return $this->getFeeByOrder("DESC");
    }

    public function getFirstFee()
    {
        return $this->getFeeByOrder("ASC");
    }

    public function isActive()
    {
        $expirationDate = $this->getExpirationDate();

        if (!$expirationDate) {
            return false;
        }

        $today = new DateTime();

        return $today < $expirationDate;
    }

    public function getMembershipStatus()
    {
        return ($this->isActive()) ? "Active" : "Inactive";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attendedMeetings()
    {
        return $this->belongsToMany('Angelov\Eestec\Platform\Entities\Meeting');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdMeetings()
    {
        return $this->hasMany('Angelov\Eestec\Platform\Entities\Meeting');
    }
}
