<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2016, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Members;

use Angelov\Storgman\Faculties\Faculty;
use Angelov\Storgman\Meetings\Meeting;
use Angelov\Storgman\Membership\Fee;
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

    protected $table = 'members';

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

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->setAttribute('email', $email);
    }

    public function getPassword()
    {
        return $this->getAttribute('password');
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->setAttribute('password', $password);
    }

    public function getFirstName()
    {
        return $this->getAttribute('first_name');
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->setAttribute('first_name', $firstName);
    }

    public function getLastName()
    {
        return $this->getAttribute('last_name');
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->setAttribute('last_name', $lastName);
    }

    public function getFullName()
    {
        return $this->getFirstName() . " " . $this->getLastName();
    }

    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * @return Faculty
     */
    public function getFaculty()
    {
        return $this->faculty;
    }

    public function setFaculty(Faculty $faculty)
    {
        $this->faculty()->associate($faculty);
    }

    public function getFieldOfStudy()
    {
        return $this->getAttribute('field_of_study');
    }

    /**
     * @param string $field
     */
    public function setFieldOfStudy($field)
    {
        $this->setAttribute('field_of_study', $field);
    }

    public function getYearOfGraduation()
    {
        return $this->getAttribute('year_of_graduation');
    }

    /**
     * @param int $year
     */
    public function setYearOfGraduation($year)
    {
        $this->setAttribute('year_of_graduation', $year);
    }

    public function getPhoto()
    {
        $photo = $this->getAttribute('photo');

        return ($photo) ? $photo : "default-member-photo.png";
    }

    /**
     * @param string $photoFileName
     */
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

    /**
     * @return int
     */
    public function getAge()
    {
        return $this->getBirthday()->age;
    }

    public function isBoardMember()
    {
        return $this->getAttribute('board_member');
    }

    /**
     * @param boolean $isBoardMember
     */
    public function setBoardMember($isBoardMember)
    {
        $this->setAttribute('board_member', $isBoardMember);
    }

    public function getPositionTitle()
    {
        return $this->getAttribute('position_title');
    }

    /**
     * @param string $title
     */
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

    /**
     * @param string $profile
     */
    public function setFacebook($profile)
    {
        $this->setAttribute('facebook', $profile);
    }

    public function getTwitter()
    {
        return $this->getAttribute('twitter');
    }

    /**
     * @param string $profile
     */
    public function setTwitter($profile)
    {
        $this->setAttribute('twitter', $profile);
    }

    public function getGooglePlus()
    {
        return $this->getAttribute('google_plus');
    }

    /**
     * @param string $profile
     */
    public function setGooglePlus($profile)
    {
        $this->setAttribute('google_plus', $profile);
    }

    public function getPhoneNumber()
    {
        return $this->getAttribute('phone');
    }

    /**
     * @param string $number
     */
    public function setPhoneNumber($number)
    {
        $this->setAttribute('phone', $number);
    }

    public function getWebsite()
    {
        return $this->getAttribute('website');
    }

    /**
     * @param string $url
     */
    public function setWebsite($url)
    {
        $this->setAttribute('website', $url);
    }

    public function isAlumniMember()
    {
        return $this->getAttribute('alumni');
    }

    /**
     * @param boolean $isAlumni
     */
    public function setAlumniMember($isAlumni)
    {
        $this->setAttribute('alumni', $isAlumni);
    }

    public function isApproved()
    {
        return $this->getAttribute('approved');
    }

    /**
     * @param boolean $isApproved
     */
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
        return $this->hasMany(Fee::class);
    }

    /**
     * @return Fee[]
     */
    public function getFees()
    {
        return $this->fees;
    }

    /**
     * @return Carbon
     */
    public function getExpirationDate()
    {
        $fee = $this->getLatestFee();

        return (!$fee) ? null : $fee->getToDate();
    }

    /**
     * @return Carbon
     */
    public function getJoiningDate()
    {
        $fee = $this->getFirstFee();

        return ($fee) ? $fee->getFromDate() : $this->getCreatedAt();
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

    /**
     * @return Fee
     */
    public function getLatestFee()
    {
        return $this->getFeeByOrder("DESC");
    }

    /**
     * @return Fee
     */
    public function getFirstFee()
    {
        return $this->getFeeByOrder("ASC");
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        $expirationDate = $this->getExpirationDate();

        if (!$expirationDate) {
            return false;
        }

        $today = new DateTime();

        return $today < $expirationDate;
    }

    /**
     * @return string
     */
    public function getMembershipStatus()
    {
        return ($this->isActive()) ? "Active" : "Inactive";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function attendedMeetings()
    {
        return $this->belongsToMany(Meeting::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function createdMeetings()
    {
        return $this->hasMany(Meeting::class);
    }
}
