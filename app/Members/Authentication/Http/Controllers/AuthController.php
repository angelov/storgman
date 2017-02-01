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

namespace Angelov\Storgman\Members\Authentication\Http\Controllers;

use Angelov\Storgman\Core\Http\Controllers\BaseController;
use Angelov\Storgman\Members\Member;
use Angelov\Storgman\Members\Authentication\Http\Requests\LoginFormRequest;
use Angelov\Storgman\Members\SocialProfiles\Repositories\SocialProfilesRepositoryInterface;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

class AuthController extends BaseController
{
    protected $authenticator;

    public function __construct(Guard $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Display the login form
     *
     * @return View
     */
    public function index()
    {
        return view('auth.index');
    }

    /**
     * Check the login data and authenticate the member.
     * Thanks to reddit.com/user/baileylo for the suggestions.
     *
     * @param LoginFormRequest $request
     * @return RedirectResponse
     */
    public function login(LoginFormRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = ($request->get('remember') == 'yes');

        if (!$this->authenticator->attempt($credentials, $remember)) {
            return $this->redirectBackWithError('Wrong email or password.');
        }

        /** @var Member $member */
        $member = $this->authenticator->user();

        return $this->proceedLogin($member);
    }

    /**
     * Return the user to the previous page and show an error
     *
     * @param string $error
     * @return RedirectResponse
     */
    protected function redirectBackWithError($error)
    {
        if ($this->authenticator->check()) {
            $this->authenticator->logout();
        }

        session()->flash('auth-error', $error);

        return redirect()->back()->withInput();
    }

    /**
     * Logout the member
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        $this->authenticator->logout();

        return redirect()->route('auth');
    }

    public function loginWithFacebook(SocialiteFactory $socialite)
    {
        $fb = $socialite->driver('facebook');
        return $fb->redirect();
    }

    public function proceedFacebookLogin(SocialiteFactory $socialite, SocialProfilesRepositoryInterface $socialProfiles)
    {
        $fb = $socialite->driver('facebook');

        $profileId = $fb->user()->getId();

        $profile = $socialProfiles->getByProfileIdAndProvider($profileId, "facebook");

        if (!$profile) {
            session()->flash()->flash('auth-error', 'Have you connected your account with Facebook?');
            return redirect()->route('auth');
        }

        $member = $profile->getMember();

        $this->authenticator->login($member);

        return redirect()->route('homepage');
    }

    protected function proceedLogin(Member $member)
    {
        if (!$member->isApproved()) {
            return $this->redirectBackWithError('Your account is not approved yet.');
        }

        return redirect()->route('homepage');
    }
}
