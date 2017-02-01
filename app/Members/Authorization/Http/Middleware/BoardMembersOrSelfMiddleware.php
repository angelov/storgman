<?php

/**
 * Storgman - Student Organizations Management
 * Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
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
 * @copyright Copyright (C) 2014, Dejan Angelov <angelovdejan92@gmail.com>
 * @license https://github.com/angelov/storgman/blob/master/LICENSE
 * @author Dejan Angelov <angelovdejan92@gmail.com>
 */

namespace Angelov\Storgman\Members\Authorization\Http\Middleware;

use Angelov\Storgman\Members\Member;
use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class BoardMembersOrSelfMiddleware
{
    protected $guard;

    public function __construct(Guard $auth)
    {
        $this->guard = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Member $member */
        $member = $this->guard->user();
        $id = $request->route()->getParameter('id');

        if (!$member->isBoardMember() && $id != $member->id) {
            return \Redirect::to('/');
        }

        return $next($request);
    }
}
