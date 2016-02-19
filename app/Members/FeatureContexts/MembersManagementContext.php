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

namespace Angelov\Eestec\Platform\Members\FeatureContexts;

use Angelov\Eestec\Platform\Core\FeatureContexts\BaseContext;
use Angelov\Eestec\Platform\Members\Member;
use Behat\Gherkin\Node\TableNode;

class MembersManagementContext extends BaseContext
{
    /** @var $members Member[] */
    private $members;
    private $credentials;

    /**
     * @Given /^there are the following members:$/
     */
    public function thereAreTheFollowingMembers(TableNode $members)
    {
        $repository = $this->getMembersRepository();
        $hasher = $this->getPasswordHasher();

        foreach ($members as $current) {
            $member = new Member();
            $member->setFirstName($current['first_name']);
            $member->setLastName($current['last_name']);
            $member->setEmail($current['email']);
            $member->setPassword($hasher->make($current['password']));
            $member->setApproved(true);

            if ($current['board'] === 'true') {
                $member->setBoardMember(true);
            }

            $repository->store($member);
            $this->members[] = $member;
            $this->keepMemberCredentials($member, $current);
        }
    }

    private function keepMemberCredentials(Member $member, $data)
    {
        $this->credentials[$member->getId()] = [
            'email' => $data['email'],
            'password' => $data['password']
        ];
    }

    private function getLoginPath()
    {
        return $this->getUrlGenerator()->route('auth');
    }

    /**
     * @Given /^I am on the login page$/
     * @When /^I go to the login page$/
     */
    public function iAmOnTheLoginPage()
    {
        $this->visitPath($this->getLoginPath());
    }

    /**
     * @Then /^I should be on the login page$/
     */
    public function iShouldBeOnTheLoginPage()
    {
        $loginPath = $this->getLoginPath();
        $this->assertSession()->addressEquals($loginPath);
    }

    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        $this->visitPath($this->getLogoutPath());
    }

    private function getLogoutPath()
    {
        return $this->getUrlGenerator()->route('logout');
    }

    /**
     * @Given I am logged in as (a )board member
     */
    public function iAmLoggedInAsABoardMember()
    {
        $member = $this->findBoardMember();
        $credentials = $this->getMemberCredentials($member);

        $loginPath = $this->getLoginPath();
        $this->visitPath($loginPath);

        $page = $this->getPage();

        $page->fillField('Email address', $credentials['email']);
        $page->fillField('Password', $credentials['password']);
        $page->pressButton('Sign in');
    }

    private function findBoardMember()
    {
        $count = count($this->members);
        for ($i=0; $i<$count; $i++) {
            $member = $this->members[$i];

            if ($member->isBoardMember()) {
                return $member;
            }
        }

        throw new \Exception("There are no board members.");
    }

    private function getMemberCredentials(Member $member)
    {
        $id = $member->getId();
        return $this->credentials[$id];
    }
}