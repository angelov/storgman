<?php

// Everything in this file is just for experimental purposes!

use Angelov\Eestec\Platform\Members\Member;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Illuminate\Contracts\Hashing\Hasher;

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
     * @Given /^there are the following users:$/
     */
    public function thereAreTheFollowingUsers(TableNode $table)
    {
        $faker = Faker\Factory::create();
        $hasher = app()->make(Hasher::class);

        foreach ($table as $row) {
            $member = new Member();

            $member->setEmail($row['email']);
            $member->setPassword($hasher->make($row['password']));
            $member->setFirstName($faker->firstName);
            $member->setLastName($faker->lastName);
            $member->setFaculty("Faculty");
            $member->setFieldOfStudy("Field of study");
            $member->setYearOfGraduation($faker->numberBetween(2015, 2018));

            $member->setApproved(true);

            $social = strtolower($member->getFirstName() . $member->getLastName());

            if ($faker->boolean(60)) {
                $member->setFacebook($social);
            }

            if ($faker->boolean(60)) {
                $member->setTwitter($social);
            }

            if ($faker->boolean(60)) {
                $member->setGooglePlus($social);
            }

            $member->setPhoneNumber($faker->phoneNumber);
            $member->setWebsite("http://". $social .".com");

            $birthday = $faker->dateTimeBetween("-20 years", "now");

            $member->setBirthday($birthday);
            $member->setBoardMember($row['board'] == "true");

            $member->save();
        }
    }

    /**
     * @Given /^I am on the login page$/
     */
    public function iAmOnTheLoginPage()
    {
        $loginPath = route('auth');
        $this->visit($loginPath);
    }
}
