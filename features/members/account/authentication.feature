@members @database
Feature: Authentication
  In order to use the application
  As an organization member
  I need to sign in

  Background:
    Given there are the following members:
      | first_name | last_name | email               | password | board |
      | Dejan      | Angelov   | angelov@example.org | 123456   | true  |
      | Somebody   | Cool      | coolguy@example.org | 123456   | false |
    And I am not logged in

  Scenario: Opening the homepage as non-authenticated user
    Given I am on the homepage
    Then I should be on the login page
    And I should see "Please Sign In"

  Scenario: Login as board member
    Given I am on the login page
    When I fill in the following:
      | Email address | angelov@example.org |
      | Password      | 123456              |
    And I press "Sign in"
    Then I should be on the homepage
    And I should see "Logout"

  Scenario: Login as regular member
    Given I am on the login page
    When I fill in the following:
      | Email address | coolguy@example.org |
      | Password      | 123456              |
    And I press "Sign in"
#    Then I should be on my profile page
    And I should see "Somebody Cool"

  Scenario: Trying to login without credentials
    Given I am on the login page
    When I press "Sign in"
    Then I should see "Please insert valid information."

  Scenario: Trying to login with wrong credentials
    Given I am on the login page
    And I fill in the following:
      | Email address | contact@angelovdejan.me |
      | Password      | 123456                  |
    When I press "Sign in"
    Then I should see "Wrong email or password."

  Scenario: Try to login when already authenticated
    Given I am on the homepage
    And I am logged in as a board member
    When I go to the login page
    Then I should be on the homepage