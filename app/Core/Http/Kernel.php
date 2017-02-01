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

namespace Angelov\Storgman\Core\Http;

use Angelov\Storgman\Core\Http\Middleware\AjaxOnlyMiddleware;
use Angelov\Storgman\Core\Http\Middleware\VerifyCsrfToken;
use Angelov\Storgman\Members\Authentication\Http\Middleware\Authenticate;
use Angelov\Storgman\Members\Authentication\Http\Middleware\RedirectIfAuthenticated;
use Angelov\Storgman\Members\Authorization\Http\Middleware\BoardMembersOnlyMiddleware;
use Angelov\Storgman\Members\Authorization\Http\Middleware\BoardMembersOrSelfMiddleware;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        CheckForMaintenanceMode::class,
        EncryptCookies::class,
        AddQueuedCookiesToResponse::class,
        StartSession::class,
        ShareErrorsFromSession::class,
        VerifyCsrfToken::class,
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'              => Authenticate::class,
        'auth.basic'        => AuthenticateWithBasicAuth::class,
        'guest'             => RedirectIfAuthenticated::class,
        'boardMember'       => BoardMembersOnlyMiddleware::class,
        'ajax'              => AjaxOnlyMiddleware::class,
        'boardMemberOrSelf' => BoardMembersOrSelfMiddleware::class,
    ];
}
