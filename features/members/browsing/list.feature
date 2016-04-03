@members @database
Feature: Listing the members
  In order to find a specific member
  As a board member
  I need to see a list of members

  Background:
    Given there are the following members:
      | first_name | last_name  | faculty | field            | email               | password | board | approved |
      | Dejan      | Angelov    | FCSE    | Computer Science | angelov@example.org | 123456   | yes   | yes      |
      | Reynold    | Kozey      | FCSE    | Networking       | reynold@example.org | 123456   | no    | yes      |
      | Raina      | McCullough | FEIT    | Electronics      | raina@example.org   | 123456   | no    | no       |
    And I am logged in as board member

  Scenario: Coming from the homepage
    Given I am on the homepage
    When I follow "Members"
    Then I should be on the members page

  Scenario: Seeing the number of members
    Given I am on the members page
    Then I should see "There are total 3 members."
    And I should see "1 pending approvals"

  Scenario: Seeing the table of members
    Given I am on the members page
    Then I should see the following table:
      | Full name                   | Faculty | Field of study   |
      | Raina McCullough Unapproved | FEIT    | Electronics      |
      | Reynold Kozey               | FCSE    | Networking       |
      | Dejan Angelov               | FCSE    | Computer Science |

  Scenario: Seeing paginated results
    Given there are 20 members
    And I am on the members page
    Then I should see a table with 15 rows

  Scenario: Seeing the results on another page
    Given there are 20 members
    And I am on the members page
    When I go to page 2
    Then I should see a table with 5 rows

  Scenario: Opening a non-existing results page
    Given there are 20 members
    And I am on the members page
    When I go to page 3
    Then I should see a "Page not found" error