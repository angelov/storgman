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

namespace Angelov\Eestec\Platform\Members\Authentication\FeatureContexts;

use Angelov\Eestec\Platform\Core\FeatureContexts\BaseContext;

class AuthenticationContext extends BaseContext
{
    /**
     * @Given /^I am on the login page$/
     */
    public function iAmOnTheLoginPage()
    {
        $generator = $this->getUrlGenerator();
        $loginPath = $generator->route('auth');
        $this->visitPath($loginPath);
    }

    /**
     * @Then /^I should be on the login page$/
     */
    public function iShouldBeOnTheLoginPage()
    {
        $generator = $this->getUrlGenerator();
        $loginPath = $generator->route('auth');
        $this->assertSession()->addressEquals($loginPath);
    }

    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        \Auth::logout();
    }

    /**
     * @Given /^I am logged in as a board member$/
     */
    public function iAmLoggedInAsABoardMember()
    {
        $repository = $this->getMembersRepository();
        $boardMembers = $repository->getBoardMembers();

        if (! count($boardMembers)) {
            throw new \Exception("There are no board members in the database.");
        }

        $member = $boardMembers[0];

        \Auth::login($member, true);
    }

    /**
     * @When /^I go to the login page$/
     */
    public function iGoToTheTheLoginPage()
    {
        $generator = $this->getUrlGenerator();
        $loginPath = $generator->route('auth');
        $this->visitPath($loginPath);
    }
}
