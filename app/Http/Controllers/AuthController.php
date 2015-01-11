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

namespace Angelov\Eestec\Platform\Http\Controllers;

use Angelov\Eestec\Platform\Entities\Member;
use Angelov\Eestec\Platform\Http\Requests\LoginFormRequest;
use Angelov\Eestec\Platform\Services\MembershipService;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;

class AuthController extends BaseController
{
    protected $request;
    protected $view;
    protected $session;
    protected $authenticator;
    protected $redirector;

    public function __construct(
        Factory $view,
        Store $session,
        Request $request,
        Guard $authenticator,
        Redirector $redirector
    ) {
        $this->request = $request;
        $this->view = $view;
        $this->session = $session;
        $this->authenticator = $authenticator;
        $this->redirector = $redirector;
    }

    /**
     * Display the login form
     *
     * @return Response
     */
    public function index()
    {
        return $this->view->make('auth.index');
    }

    /**
     * Check the login data and authenticate the member.
     * Thanks to reddit.com/user/baileylo for the suggestions.
     *
     * @param LoginFormRequest $request
     * @param MembershipService $membershipService
     * @return Response
     */
    public function login(LoginFormRequest $request, MembershipService $membershipService)
    {
        $credentials = $request->only('email', 'password');
        $remember = ($request->get('remember') == 'yes');

        if (!$this->authenticator->attempt($credentials, $remember)) {
            $this->session->flash('auth-error', 'Wrong email or password.');

            return $this->redirector->back()->withInput();
        }

        /** @var Member $member */
        $member = $this->authenticator->user();

        if (!$member->approved) {
            $this->authenticator->logout();
            $this->session->flash('auth-error', 'Your account is not approved yet.');

            return $this->redirector->back()->withInput();
        }

        if (!$membershipService->isMemberActive($member)) {
            $this->authenticator->logout();
            $this->session->flash('auth-error', 'Your membership needs to be reactivated. Have you paid the fees?');

            return $this->redirector->back();
        }

        return $this->redirector->to('/');

    }

    /**
     * Logout the member
     *
     * @return Response
     */
    public function logout()
    {
        $this->authenticator->logout();

        return $this->redirector->to('/');
    }

}
