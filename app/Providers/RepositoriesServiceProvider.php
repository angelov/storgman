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

namespace Angelov\Eestec\Platform\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    protected $container;

    public function __construct(Application $container) {
        $this->container = $container;
    }

    /**
     * Bind the repository interfaces to some implementations
     *
     * @return void
     */
    public function register()
    {
        $this->container->bind('Angelov\Eestec\Platform\Repositories\MembersRepositoryInterface',
            'Angelov\Eestec\Platform\Repositories\EloquentMembersRepository');

        $this->container->bind('Angelov\Eestec\Platform\Repositories\FeesRepositoryInterface',
            'Angelov\Eestec\Platform\Repositories\EloquentFeesRepository');

        $this->container->bind('Angelov\Eestec\Platform\Repositories\PhotosRepositoryInterface',
            'Angelov\Eestec\Platform\Repositories\LocalPhotosRepository');

        $this->container->bind('Angelov\Eestec\Platform\Repositories\MeetingsRepositoryInterface',
            'Angelov\Eestec\Platform\Repositories\EloquentMeetingsRepository');

        $this->container->bind('Angelov\Eestec\Platform\Repositories\DocumentsRepositoryInterface',
            'Angelov\Eestec\Platform\Repositories\EloquentDocumentsRepository');

        $this->container->bind('Angelov\Eestec\Platform\Repositories\TagsRepositoryInterface',
            'Angelov\Eestec\Platform\Repositories\EloquentTagsRepository');

        $this->container->bind('Angelov\Eestec\Platform\Repositories\SocialProfilesRepositoryInterface',
            'Angelov\Eestec\Platform\Repositories\EloquentSocialProfilesRepository');

        $container = $this->container;

        $this->container->bind('PhotosRepository', function () use ($container) {
            return $container->make('Angelov\Eestec\Platform\Repositories\PhotosRepositoryInterface');
        });

    }
}
 