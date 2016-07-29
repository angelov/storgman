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

namespace Angelov\Eestec\Platform\Members\Authentication\UserProviders;

use Angelov\Eestec\Platform\Members\Member;
use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;

class DoctrineUserProvider implements UserProvider
{
    protected $em;
    protected $hasher;

    // @todo use repository instead of em
    public function __construct(EntityManager $em, Hasher $hasher)
    {
        $this->em = $em;
        $this->hasher = $hasher;
    }

    private function getRepository()
    {
        return $this->em->getRepository(Member::class);
    }

    public function retrieveById($identifier)
    {
        return $this->getRepository()->find($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        return $this->getRepository()->findOneBy(['rememberToken' => $token]);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);

        $this->em->persist($user);
        $this->em->flush();
    }

    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials)) {
            return null;
        }

        if (isset($credentials['password'])) {
            unset($credentials['password']);
        }

        return $this->getRepository()->findOneBy($credentials);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'];

        return $this->hasher->check($plain, $user->getAuthPassword());
    }
}
