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

namespace Angelov\Eestec\Platform\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Bind the repository interfaces to some implementations
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface',
            'Angelov\Eestec\Platform\Members\Repositories\EloquentMembersRepository');

        $this->app->bind('Angelov\Eestec\Platform\Membership\Repositories\FeesRepositoryInterface',
            'Angelov\Eestec\Platform\Membership\Repositories\EloquentFeesRepository');

        $this->app->bind('Angelov\Eestec\Platform\Members\Photos\Repositories\PhotosRepositoryInterface',
            'Angelov\Eestec\Platform\Members\Photos\Repositories\LocalPhotosRepository');

        $this->app->bind('Angelov\Eestec\Platform\Meetings\Repositories\MeetingsRepositoryInterface',
            'Angelov\Eestec\Platform\Meetings\Repositories\EloquentMeetingsRepository');

        $this->app->bind('Angelov\Eestec\Platform\Documents\Repositories\DocumentsRepositoryInterface',
            'Angelov\Eestec\Platform\Documents\Repositories\EloquentDocumentsRepository');

        $this->app->bind('Angelov\Eestec\Platform\Documents\Tags\Repositories\TagsRepositoryInterface',
            'Angelov\Eestec\Platform\Documents\Tags\Repositories\EloquentTagsRepository');

        $this->app->bind('Angelov\Eestec\Platform\Members\SocialProfiles\Repositories\SocialProfilesRepositoryInterface',
            'Angelov\Eestec\Platform\Members\SocialProfiles\Repositories\EloquentSocialProfilesRepository');

        $container = $this->app;

        $this->app->bind('PhotosRepository', function () use ($container) {
            return $container->make('Angelov\Eestec\Platform\Members\Photos\Repositories\PhotosRepositoryInterface');
        });
    }
}
