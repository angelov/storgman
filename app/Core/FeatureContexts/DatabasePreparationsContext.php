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

namespace Angelov\Storgman\Core\FeatureContexts;

class DatabasePreparationsContext extends BaseContext
{
    // May the Force be with us
    private static $force = ['--force' => true];

    /**
     * @BeforeFeature
     */
    public static function prepareDatabase()
    {
        $artisan = (new self)->getArtisan();

        $artisan->call('migrate:reset', self::$force);
        $artisan->call('migrate', self::$force);
    }

    /**
     * @BeforeScenario @database
     */
    public function refreshDatabase()
    {
        $this->getArtisan()->call('migrate:refresh', self::$force);
    }

    /**
     * @AfterFeature
     */
    public static function destroyDatabase()
    {
        (new self)->getArtisan()->call('migrate:reset', self::$force);
    }

    private static function getDatabaseName()
    {
        return env('DB_DATABASE');
    }
}