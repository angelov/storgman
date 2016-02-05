Feature: Authentication
  In order to use the application
  As an organization member
  I need to sign in

  Background:
    Given there are the following members:
      | first_name | last_name | email               | password | board |
      | Dejan      | Angelov   | angelov@example.org | 123456   | false |

  Scenario: Login with valid credentials
    Given I am on the login page
    When I fill in the following:
      | Email address | angelov@example.org |
      | Password      | 123456              |
    And I press "Sign in"
    Then I should see "Logout"