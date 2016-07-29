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

namespace Angelov\Eestec\Platform\Members;

use Angelov\Eestec\Platform\Faculties\Faculty;
//use Angelov\Eestec\Platform\Meetings\Meeting;
//use Angelov\Eestec\Platform\Membership\Fee;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="member")
 */
class Member implements AuthenticatableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $rememberToken;

    /**
     * @ORM\Column(type="string")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string")
     */
    private $lastName;

    // @todo relation
    private $faculty;

    /**
     * @ORM\Column(type="string")
     */
    private $fieldOfStudy;

    /**
     * @ORM\Column(type="integer")
     */
    private $graduationYear;

    /**
     * @ORM\Column(type="string")
     */
    private $photo;

    /**
     * @ORM\Column(type="date")
     */
    private $birthday;

    // age

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $boardMember;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $positionTitle;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

//    private $updatedAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $googlePlus;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $alumni;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     */
    private $approved;

    public function __construct()
    {
        $this->createdAt = new Carbon();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getFullname()
    {
        return sprintf(
            "%s %s",
            $this->getFirstName(),
            $this->getLastName()
        );
    }

    /**
     * @return Faculty
     */
    public function getFaculty()
    {
        return $this->faculty;
    }

    /**
     * @param Faculty $faculty
     */
    public function setFaculty(Faculty $faculty)
    {
        $this->faculty = $faculty;
    }

    public function getFieldOfStudy()
    {
        return $this->fieldOfStudy;
    }

    public function setFieldOfStudy($fieldOfStudy)
    {
        $this->fieldOfStudy = $fieldOfStudy;
    }

    public function getGraduationYear()
    {
        return $this->graduationYear;
    }

    public function setGraduationYear($graduationYear)
    {
        $this->graduationYear = $graduationYear;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return Carbon
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday(Carbon $birthday)
    {
        $this->birthday = $birthday;
    }

    public function getAge()
    {
        return $this->getBirthday()->age;
    }

    public function isBoardMember()
    {
        return $this->boardMember == true;
    }

    public function setBoardMember($isBoardMember)
    {
        $this->boardMember = $isBoardMember == true;
    }

    public function getPositionTitle()
    {
        return $this->positionTitle;
    }

    public function setPositionTitle($positionTitle)
    {
        $this->positionTitle = $positionTitle;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getFacebook()
    {
        return $this->facebook;
    }

    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    public function getTwitter()
    {
        return $this->twitter;
    }

    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    public function getGooglePlus()
    {
        return $this->googlePlus;
    }

    public function setGooglePlus($googlePlus)
    {
        $this->googlePlus = $googlePlus;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function setWebsite($website)
    {
        $this->website = $website;
    }

    public function isAlumni()
    {
        return $this->alumni == true;
    }

    public function setAlumni($isAlumni)
    {
        $this->alumni = $isAlumni;
    }

    public function isApproved()
    {
        return $this->approved == true;
    }

    public function setApproved($isApproved)
    {
        $this->approved = $isApproved;
    }


    // ==========================================================================
//
//    /**
//     * Membership fees paid by the member
//     *
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function fees()
//    {
//        return $this->hasMany(Fee::class);
//    }
//
//    /**
//     * @return Fee[]
//     */
//    public function getFees()
//    {
//        return $this->fees;
//    }
//
//    /**
//     * @return Carbon
//     */
//    public function getExpirationDate()
//    {
//        $fee = $this->getLatestFee();
//
//        return (!$fee) ? null : $fee->getToDate();
//    }
//
//    /**
//     * @return Carbon
//     */
//    public function getJoiningDate()
//    {
//        $fee = $this->getFirstFee();
//
//        return ($fee) ? $fee->getFromDate() : $this->getCreatedAt();
//    }
//
//    /**
//     * @param string $order ASC or DESC
//     * @return Fee
//     */
//    private function getFeeByOrder($order)
//    {
//        $fee = $this->fees()->orderBy('to_date', $order)->first();
//
//        return $fee;
//    }
//
//    /**
//     * @return Fee
//     */
//    public function getLatestFee()
//    {
//        return $this->getFeeByOrder("DESC");
//    }
//
//    /**
//     * @return Fee
//     */
//    public function getFirstFee()
//    {
//        return $this->getFeeByOrder("ASC");
//    }
//
//    /**
//     * @return bool
//     */
//    public function isActive()
//    {
//        $expirationDate = $this->getExpirationDate();
//
//        if (!$expirationDate) {
//            return false;
//        }
//
//        $today = new DateTime();
//
//        return $today < $expirationDate;
//    }
//
//    /**
//     * @return string
//     */
//    public function getMembershipStatus()
//    {
//        return ($this->isActive()) ? "Active" : "Inactive";
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
//     */
//    public function attendedMeetings()
//    {
//        return $this->belongsToMany(Meeting::class);
//    }
//
//    /**
//     * @return \Illuminate\Database\Eloquent\Relations\HasMany
//     */
//    public function createdMeetings()
//    {
//        return $this->hasMany(Meeting::class);
//    }

    public function getAuthIdentifierName()
    {
        return "id";
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return "email";
    }

    public function getAuthPassword()
    {
        return $this->getPassword();
    }

    public function getRememberToken()
    {
        return $this->rememberToken;
    }

    public function setRememberToken($value)
    {
        $this->rememberToken = $value;
    }

    public function getRememberTokenName()
    {
        return "rememberToken";
    }
}
