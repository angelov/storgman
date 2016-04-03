@members @database
Feature: Authorization
  In order to browse the members
  As a member
  I need to have a boar member privilegies

  Background:
    Given there are the following members:
      | first_name | last_name  | faculty | field            | email               | password | board | approved |
      | Dejan      | Angelov    | FCSE    | Computer Science | angelov@example.org | 123456   | yes   | yes      |
      | Reynold    | Kozey      | FCSE    | Networking       | reynold@example.org | 123456   | no    | yes      |

  Scenario: Try to access as non-logged user
    Given I am not logged in
    When I go to the members page
    Then I should be on the login page

  Scenario: Try to access as regular member
    Given I am logged in as "Reynold Kozey"
    When I go to the members page
    Then I should be on my profile page

  Scenario: Try to access as a board member
    Given I am logged in as "Dejan Angelov"
    When I go to the members page
    Then I should be on the members page
