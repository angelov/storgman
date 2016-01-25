<?php

use Angelov\Eestec\Platform\Members\Member;
use Angelov\Eestec\Platform\Members\Repositories\MembersRepositoryInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Illuminate\Contracts\Hashing\Hasher;
use Laracasts\Behat\Context\Migrator;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    use Migrator;

    /**
     * @Given /^I am logged in as a board member$/
     */
    public function iAmLoggedInAsABoardMember()
    {
        // experimental
        \Auth::attempt(['email' => 'admin@ultim8.info', 'password' => '123456']);
    }

    /**
     * @Given /^I am on the "([^"]*)" path$/
     */
    public function iAmOnThePath($path)
    {
        $this->visitPath($path);

        // experimental
        // selenium2 driver doesn't support getting the status code
        $currentUrl = $this->getSession()->getCurrentUrl();
        $guzzleClient = new \GuzzleHttp\Client();
        $response = $guzzleClient->get($currentUrl);
        $statusCode = $response->getStatusCode();

        if ($statusCode != 200) {
            throw new \Exception(sprintf(
                "Could not open path: \"%s\". Code: %d",
                $path,
                $statusCode
            ));
        }
    }

    /**
     * @Given /^I wait for the modal window to open$/
     */
    public function iWaitForTheModalWindowToOpen()
    {
        $this->getSession()->wait(1000, "$('.modal')");
    }

    /**
     * @Then /^the modal window should disappear$/
     */
    public function theModalWindowShouldDisappear()
    {
        $this->getSession()->wait(500);

        if ($this->getSession()->evaluateScript("return $('.modal').is(':visible')")) {
            throw new \Exception('The modal window did not disappear.');
        }
    }

    /**
     * @Given /^I am on the login page$/
     */
    public function iAmOnTheLoginPage()
    {
        $this->iAmOnThePath("/auth");
    }

    /**
     * @Given /^I have the following members:$/
     */
    public function iHaveTheFollowingMembers(TableNode $members)
    {
        /** @var MembersRepositoryInterface $members */
        $membersRepository = app()->make(MembersRepositoryInterface::class);

        /** @var Hasher $hasher */
        $hasher = app()->make(Hasher::class);

        foreach ($members as $current) {
            $member = new Member();
            $member->setFirstName($current['first_name']);
            $member->setLastName($current['last_name']);
            $member->setEmail($current['email']);
            $member->setPassword($hasher->make($current['password']));

            if ($member['type'] === 'board') {
                $member->setBoardMember(true);
            }

            $membersRepository->store($member);
        }
    }

    /**
     * @When /^I login as "([^"]*)"$/
     */
    public function iLoginAs($name)
    {

    }
}
