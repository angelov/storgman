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

namespace Angelov\Eestec\Platform\LocalCommittees\Cities\Handlers;

use Angelov\Eestec\Platform\LocalCommittees\Cities\City;
use Angelov\Eestec\Platform\LocalCommittees\Cities\Commands\StoreCityCommand;
use Angelov\Eestec\Platform\LocalCommittees\Cities\Repositories\CitiesRepositoryInterface;

class StoreCityCommandHandler
{
    protected $cities;

    public function __construct(CitiesRepositoryInterface $cities)
    {
        $this->cities = $cities;
    }

    public function handle(StoreCityCommand $command)
    {
        $city = new City();

        $city->setName($command->getName());
        $city->setCountry($command->getCountry());
        $city->setLocation($command->getLocation());
        $city->setDetails($command->getDetails());

        // @todo refactor
        $file = $command->getImage();
        $filename = md5($file->getBasename()) . "_" . md5(rand(0, 10000)) . "." . $file->getExtension();
        $file->move(storage_path("photos/local-committees/cities"), $filename);
        $city->setImage($filename);

        $this->cities->store($city);
    }
}
