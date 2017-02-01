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

use Angelov\Storgman\Events\Commands\StoreEventCommand;
use Angelov\Storgman\Events\EventImage;
use Angelov\Storgman\LocalCommittees\Repositories\LocalCommitteesRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FakeEventsSeeder extends Seeder
{
    protected $faker;
    protected $localCommittees;

    public function __construct(\Faker\Factory $fakerFactory, LocalCommitteesRepositoryInterface $localCommittees)
    {
        $this->faker = $fakerFactory->create();
        $this->localCommittees = $localCommittees;
    }

    public function run()
    {
        for ($i=0; $i<5; $i++) {

            $title = $this->faker->catchPhrase();
            $description = $this->faker->realText();

            $lcs = $this->localCommittees->all();
            $host = $lcs[rand(0, count($lcs)-1)];

            $url = $this->faker->url();

            $image = $this->faker->image($dir = '/tmp', $width = 640, $height = 480);
            $image = new EventImage("eventimage.jpg", $image);

            $startDate = new Carbon($this->faker->dateTimeBetween('now', '+1 month')->format('d-m-Y'));
            $endDate = clone  $startDate;
            $endDate->addDays(rand(7, 10));
            $deadline = (new Carbon())->addDays(rand(3, 10));

            dispatch(new StoreEventCommand($title, $description, $host->getId(), $url, $image, $startDate, $endDate, $deadline));



        }
    }
}