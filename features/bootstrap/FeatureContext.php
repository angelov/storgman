<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Defines application features from the specific context.
 */
class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
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
}
