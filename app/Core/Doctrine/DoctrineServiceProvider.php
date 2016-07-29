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

namespace Angelov\Eestec\Platform\Core\Doctrine;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\Tools\Setup;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\ServiceProvider;

class DoctrineServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var Repository $config */
        $config = $this->app->make(Repository::class);

        $driver = $config->get('database.default');
        $isDevMode = $config->get('app.debug');

        $dbParams = array(
            'driver'   => 'pdo_' . $driver,
            'user'     => $config->get('database.connections.' . $driver . '.username'),
            'password' => $config->get('database.connections.' . $driver . '.password'),
            'dbname'   => $config->get('database.connections.' . $driver . '.database'),
        );

        $modules = [
            'Members',
            'Members/Authentication',
            'Membership',
            'Meetings',
            'Meetings/Attachments',
            'Documents',
            'News',
            'Events',
            'Events/Comments',
            'Settings',
        ];

        $paths = [];

        foreach ($modules as $module) {
            $paths[] = app_path() .'/'. $module;
        }

        $cache = new ArrayCache();

        $reader = new AnnotationReader();
        $driver = new AnnotationDriver($reader, $paths);

        $configuration = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $configuration->setMetadataCacheImpl($cache);
        $configuration->setQueryCacheImpl($cache);
        $configuration->setMetadataDriverImpl($driver);

        $entityManager = EntityManager::create($dbParams, $configuration);

        $this->app->singleton(EntityManager::class, function () use ($entityManager) {
            return $entityManager;
        });
    }
}
