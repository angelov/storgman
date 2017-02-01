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

// Note: Mess inside.

namespace Angelov\Storgman\Members\FeatureContexts;

use Angelov\Storgman\Core\FeatureContexts\BaseContext;
use Angelov\Storgman\Members\Member;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

class MembersManagementContext extends BaseContext
{
    /** @var $members Member[] */
    private $members;

    private $credentials;

    /** @var $authenticatedMember Member */
    private $authenticatedMember;

    /**
     * @Given /^there are the following members:$/
     */
    public function thereAreTheFollowingMembers(TableNode $members)
    {
        $this->clearMembers();
        foreach ($members as $current) {
            $this->storeMember($current);
        }
    }

    private function storeMember($current)
    {
        $repository = $this->getMembersRepository();
        $hasher = $this->getPasswordHasher();

        $member = new Member();
        $member->setFirstName($current['first_name']);
        $member->setLastName($current['last_name']);
        $member->setEmail($current['email']);
        $member->setPassword($hasher->make($current['password']));

        if (isset($current['faculty'])) {
            $member->setFaculty($current['faculty']);
        }

        if (isset($current['field'])) {
            $member->setFieldOfStudy($current['field']);
        }

        $member->setApproved($current['approved'] === 'yes');
        $member->setBoardMember($current['board'] === 'yes');

        $repository->store($member);

        $this->members[] = $member;
        $this->keepMemberCredentials($member, $current);
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

        $this->authenticate($member);
    }

    private function authenticate(Member $member)
    {
        $credentials = $this->getMemberCredentials($member);

        $loginPath = $this->getLoginPath();
        $this->visitPath($loginPath);

        $page = $this->getPage();

        $page->fillField('Email address', $credentials['email']);
        $page->fillField('Password', $credentials['password']);
        $page->pressButton('Sign in');


        $this->authenticatedMember = $member;
    }

    /**
     * @todo Create separate in-memory members repository
     */
    private function findMemberByFullName($fullName)
    {
        $count = count($this->members);
        for ($i = 0; $i < $count; $i++) {
            $member = $this->members[$i];

            if ($member->getFullName() === $fullName) {
                return $member;
            }
        }

        throw new \Exception(printf(
            "There is no member with the given full name [%s].",
            $fullName
        ));
    }

    private function findBoardMember()
    {
        $count = count($this->members);
        for ($i = 0; $i < $count; $i++) {
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

    /**
     * @When /^I login as "([^"]*)"$/
     * @Given /^I am logged in as "([^"]*)"$/
     */
    public function iLoginAs($fullName)
    {
        $member = $this->findMemberByFullName($fullName);
        $this->authenticate($member);
    }

    /**
     * @Then /^I should be on my profile page$/
     */
    public function iShouldBeOnMyProfilePage()
    {
        if (!$this->authenticatedMember) {
            throw new \Exception("You must login first.");
        }

        $id = $this->authenticatedMember->getId();
        $this->assertSession()->addressEquals(route('members.show', $id));
    }

    /**
     * @Then /^I should be on the registration page$/
     */
    public function iShouldBeOnTheRegistrationPage()
    {
        $this->assertSession()->addressEquals(route('members.register'));
    }

    /**
     * @Given /^I am on the registration page$/
     * @When /^I go to the registration page$/
     */
    public function iAmOnTheRegistrationPage()
    {
        $this->visitPath(route('members.register'));
    }

    /**
     * @Given /^I am on the members page$/
     */
    public function iAmOnTheMembersPage()
    {
        $this->visitPath(route("members.index"));
    }

    /**
     * @Then /^I should be on the members page$/
     */
    public function iShouldBeOnTheMembersPage()
    {
        $this->assertSession()->addressEquals(route('members.index'));
    }

    private function clearMembers()
    {
        if (!count($this->members)) {
            return;
        }

        foreach ($this->members as $member) {
            $this->getMembersRepository()->destroy($member->getId());
        }
    }
}