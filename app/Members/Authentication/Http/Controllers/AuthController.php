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

namespace Angelov\Eestec\Platform\Members\Authentication\Http\Controllers;

use Angelov\Eestec\Platform\Core\Http\Controllers\BaseController;
use Angelov\Eestec\Platform\Members\Member;
use Angelov\Eestec\Platform\Members\Authentication\Http\Requests\LoginFormRequest;
use Angelov\Eestec\Platform\Members\SocialProfiles\Repositories\SocialProfilesRepositoryInterface;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Session\Store;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;

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
     * @return View
     */
    public function index()
    {
        return $this->view->make('auth.index');
    }

    /**
     * Check the login data and authenticate the member.
     * Thanks to reddit.com/user/baileylo for the suggestions.
     *
     * @param \Angelov\Eestec\Platform\Members\Authentication\Http\Requests\LoginFormRequest $request
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

        $this->session->flash('auth-error', $error);

        return $this->redirector->back()->withInput();
    }

    /**
     * Logout the member
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        $this->authenticator->logout();

        return $this->redirector->to('/');
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
            $this->session->flash('auth-error', 'Have you connected your account with Facebook?');
            return $this->redirector->route('auth');
        }

        $member = $profile->getMember();

        $this->authenticator->login($member);

        return $this->redirector->to('/');
    }

    protected function proceedLogin(Member $member)
    {
        if (!$member->isApproved()) {
            return $this->redirectBackWithError('Your account is not approved yet.');
        }

        return $this->redirector->to('/');
    }
}
