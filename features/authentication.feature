Feature: Authentication

  Background:
    Given I have the following members:
      | first_name | last_name | email               | password | type    |
      | Dejan      | Angelov   | dejan@eestec.local  | 123456   | board   |

  Scenario: Opening the homepage as non-authenticated user
    Given I am on the homepage
    Then I should be on "/auth"
    And I should see "Please Sign In"

  Scenario: Trying to login without credentials
    Given I am on the login page
    When I press "Sign in"
    Then I should see "Please insert valid information."

#  Scenario: Trying to login with wrong credentials
#    When I login as "Angela"
#    Then I should see "Wrong email or password."

  Scenario: Login as board member
    Given I am on the login page
    When I fill in the following:
      | email    | dejan@eestec.local |
      | password | 123456             |
    And I press "Sign in"
    Then I should be on the homepage
    And I should see "Logout"

  Scenario: Login as regular member
    Given I am on the login page
    When I fill in the following:
      | email    | example@example.com3240 |
      | password | 123456                  |
    And I press "Sign in"
    Then I should be on "/members/95"
    And I should see "First Last"
