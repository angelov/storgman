<?php

/**
 * EESTEC Platform for Local Committees
 * Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014-2015, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/eestec-platform/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

use Angelov\Eestec\Platform\Entities\Fee;
use Angelov\Eestec\Platform\Entities\Meeting;
use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Repositories\MeetingsRepositoryInterface;
use Illuminate\Database\Seeder;
use Angelov\Eestec\Platform\Repositories\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface;

class FakeDataSeeder extends Seeder
{
    protected $members;
    protected $fees;
    protected $meetings;
    protected $faker;
    protected $generatedMembers;

    public function __construct(
        MembersRepositoryInterface $members,
        FeesRepositoryInterface $fees,
        MeetingsRepositoryInterface $meetings,
        Faker\Factory $fakerFactory
    ) {
        $this->members = $members;
        $this->fees = $fees;
        $this->meetings = $meetings;
        $this->faker = $fakerFactory::create();
        $this->generatedMembers = [];
    }

    public function run()
    {
        $this->generateMembers(500);
        $this->generateFees();
        $this->generateMeetings(50);
    }

    private function generateMembers($count = 200)
    {
        print "Generating members started...\n";

        $faculties = ["Fax 1", "Fax 2", "Fax 3"];
        $fieldOfStudies = ["Computer Science", "Electrical Engineering", "Automation"];
        $birthYearFrom = "-27 years";
        $birthYearTo = "-19 years";

        for ($i = 0; $i <= $count; $i++) {

            $member = new Member();

            $member->setEmail($this->faker->email);
            $member->setPassword(Hash::make('123456'));
            $member->setFirstName($this->faker->firstName);
            $member->setLastName($this->faker->lastName);
            $member->setFaculty($this->faker->randomElement($faculties));
            $member->setFieldOfStudy($this->faker->randomElement($fieldOfStudies));
            $member->setYearOfGraduation($this->faker->numberBetween(2015, 2018));

            $member->setApproved($this->faker->boolean(90));

            $social = strtolower($member->getFirstName() . $member->getLastName());

            if ($this->faker->boolean(60)) {
                $member->setFacebook($social);
            }

            if ($this->faker->boolean(60)) {
                $member->setTwitter($social);
            }

            if ($this->faker->boolean(60)) {
                $member->setGooglePlus($social);
            }

            $member->setPhoneNumber($this->faker->phoneNumber);
            $member->setWebsite("http://". $social .".com");

            $birthday = $this->faker->dateTimeBetween($birthYearFrom, $birthYearTo);

            $member->setBirthday($birthday);
            $member->setBoardMember($this->faker->boolean(20));

            $this->generatedMembers[] = $member;

            $this->members->store($member);

        }

        print "Generated ". $count ." members.\n";
    }

    private function hasGeneratedMembers()
    {
        return (count($this->generatedMembers) > 0);
    }

    private function generateFees()
    {
        if (!$this->hasGeneratedMembers()) {
            return;
        }

        print "Generating fees started...\n";
        $fees = 0;

        foreach ($this->generatedMembers as $member) {

            $from = $this->faker->dateTimeBetween('-10 years', '-1 year');

            for ($i = 0; $i < 3; $i++) {
                $fee = new Fee();

                $fee->setFromDate($from);

                $to = clone $from;
                $to->modify('+1 year');
                $fee->setToDate($to);

                $fee->setMember($member);

                $this->fees->store($fee);
                $fees++;

                $now = new \DateTime('now');

                if ($to > $now) {
                    break;
                }

                $from->modify('+1 day');
                $from->modify('+1 year');
            }

        }

        print "Generated ". $fees ." fees.\n";
    }

    private function generateMeetings($count = 50)
    {

        if (!$this->hasGeneratedMembers()) {
            return;
        }

        print "Generating meetings started...\n";
        $attendings = 0;

        // Calculate the date for the first meeting..
        // There will be one meeting per week.
        $ago = $count * 7;
        $date = new \DateTime('-' . $ago . ' days');

        for ($i = 0; $i < $count; $i++) {
            $meeting = new Meeting();

            $meeting->setDate($date);
            $meeting->setLocation($this->faker->streetAddress);
            $meeting->setInfo("<p>This is fake meeting report with randomly generated information.</p>");

            $creator = $this->generatedMembers[0];
            $attendants = $this->pickGeneratedMembers($this->calculateNeededAttendants());
            $attendings += count($attendants);

            $meeting->setCreator($creator);
            $meeting->addAttendants($attendants);

            $this->meetings->store($meeting);

            $date->modify('+1 week');
        }

        print "Generated ". $count ." meetings with ". $attendings ." total attendings\n";

    }

    private function pickGeneratedMembers($count = 0)
    {
        $pickedIndexes = [];
        $pickedMembers = [];

        for ($i = 0; $i < $count; $i++) {

            $index = $this->faker->numberBetween(0, count($this->generatedMembers) - 1);

            if (!in_array($index, $pickedIndexes)) {
                $pickedIndexes[] = $index;
                $pickedMembers[] = $this->generatedMembers[$index];
            }

        }

        return $pickedMembers;
    }

    private function calculateNeededAttendants()
    {
        $count = count($this->generatedMembers);

        if ($count == 0) {
            return 0;
        }

        if ($count < 50) {
            return $this->faker->numberBetween(1, $count);
        }

        if ($count < 100) {
            return $this->faker->numberBetween(50, $count);
        }

        return $this->faker->numberBetween(50, 100);
    }

}
