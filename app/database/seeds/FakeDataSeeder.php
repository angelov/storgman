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

use Angelov\Eestec\Platform\Entity\Fee;
use Angelov\Eestec\Platform\Entity\Meeting;
use Angelov\Eestec\Platform\Entity\Member;
use Angelov\Eestec\Platform\Repository\MeetingsRepositoryInterface;
use Illuminate\Database\Seeder;
use Angelov\Eestec\Platform\Repository\FeesRepositoryInterface;
use Angelov\Eestec\Platform\Repository\MembersRepositoryInterface;

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
        $this->generateMembers(200);
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

            $member->email = $this->faker->email;
            $member->password = Hash::make('123456');
            $member->first_name = $this->faker->firstName;
            $member->last_name = $this->faker->lastName;
            $member->faculty = $this->faker->randomElement($faculties);
            $member->field_of_study = $this->faker->randomElement($fieldOfStudies);
            $member->year_of_graduation = $this->faker->numberBetween(2015, 2018);

            if ($this->faker->boolean(90)) {
                $member->approved = true;
            }

            $social = strtolower($member->first_name . $member->last_name);

            if ($this->faker->boolean(60)) {
                $member->facebook = $social;
            }

            if ($this->faker->boolean(60)) {
                $member->twitter = $social;
            }

            if ($this->faker->boolean(60)) {
                $member->google_plus = $social;
            }

            $member->phone = $this->faker->phoneNumber;
            $member->website = "http://". $social .".com";

            $birthday = $this->faker->dateTimeBetween($birthYearFrom, $birthYearTo);

            $member->birthday = $birthday->format("Y-m-d");
            $member->board_member = $this->faker->boolean(20);

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

                $fee->from_date = $from->format('Y-m-d');

                $to = $from->modify('+1 year');
                $fee->to_date = $to->format('Y-m-d');

                $this->fees->store($fee, $member);
                $fees++;

                $now = new \DateTime('now');

                if ($to > $now) {
                    break;
                }

                $from->modify('+1 day');
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

            $meeting->date = $date;
            $meeting->location = $this->faker->streetAddress;
            $meeting->info = "<p>This is fake meeting report with randomly generated information.</p>";

            $creator = $this->generatedMembers[0];
            $attendants = $this->pickGeneratedMembers($this->calculateNeededAttendants());
            $attendings += count($attendants);

            $this->meetings->store($meeting, $creator, $attendants);

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
