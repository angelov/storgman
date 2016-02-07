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

namespace Angelov\Eestec\Platform\Core\FeatureContexts;

use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\Routing\UrlGenerator;

abstract class BaseContext extends RawMinkContext implements Context
{
    /**
     * @return Kernel
     */
    public function getArtisan()
    {
        return app()->make(Kernel::class);
    }

    /**
     * @return Hasher
     */
    public function getPasswordHasher()
    {
        return app()->make(Hasher::class);
    }

    /**
     * @return UrlGenerator
     */
    public function getUrlGenerator()
    {
        return app()->make(UrlGenerator::class);
    }

    /**
     * @return Guard
     */
    public function getAuthenticator()
    {
        return app()->make(Guard::class);
    }

    /**
     * @return MembersRepositoryInterface
     */
    protected function getMembersRepository()
    {
        return app()->make(MembersRepositoryInterface::class);
    }
}