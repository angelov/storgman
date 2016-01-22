Feature: Check if working

  Scenario: Log in
    Given I am on the homepage
    And I fill in the following:
      | email | admin@ultim8.info |
      | password | 123456         |
    And I press "Sign in"
    Then I should see "New members per month"