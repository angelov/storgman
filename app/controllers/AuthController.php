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

use Angelov\Eestec\Platform\Service\MembershipService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Angelov\Eestec\Platform\Validation\LoginCredentialsValidator;

class AuthController extends \BaseController
{

    protected $request;
    protected $validator;

    public function __construct(Request $request, LoginCredentialsValidator $validator)
    {
        $this->request = $request;
        $this->validator = $validator;

        $this->beforeFilter('guest', ['only' => ['index', 'login']]);
        $this->beforeFilter('auth', ['only' => ['logout']]);
    }

    /**
     * Display the login form
     *
     * @return Response
     */
    public function index()
    {
        return View::make('auth.index');
    }

    /**
     * Check the login data and authenticate the member.
     * Thanks to reddit.com/user/baileylo for the suggestions.
     *
     * @return Response
     */
    public function login()
    {

        if (!$this->validator->validate($this->request->all())) {
            Session::flash('auth-error', 'Please insert valid information.');

            return Redirect::back()->withInput();
        }

        $credentials = $this->request->only('email', 'password');
        $remember = ($this->request->get('remember') == 'yes');

        if (Auth::attempt($credentials, $remember)) {
            Session::flash('auth-error', 'Wrong email or password.');

            return Redirect::back()->withInput();
        }

        /** @var MembershipService $membershipService */
        $membershipService = App::make('MembershipService');
        $member = Auth::user();

        if (!$membershipService->isMemberActive($member)) {
            Auth::logout();
            Session::flash('auth-error', 'Your membership needs to be reactivated. Have you paid the fees?');

            return Redirect::back();
        }

        return Redirect::to('/');

    }

    /**
     * Logout the member
     *
     * @return Response
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::to('/');
    }

}
