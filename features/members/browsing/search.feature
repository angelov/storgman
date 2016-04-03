@members @database @javascript
Feature: Search
  In order to see a member's details
  As a board member
  I should be able to use the search form

  Background:
    Given there are the following members:
      | first_name | last_name  | faculty | field            | email               | password | board | approved |
      | Dejan      | Angelov    | FCSE    | Computer Science | angelov@example.org | 123456   | yes   | yes      |
      | Reynold    | Kozey      | FCSE    | Networking       | reynold@example.org | 123456   | no    | yes      |
    And I am logged in as "Dejan Angelov"

  Scenario: See multiple suggestions
    Given I am on the members page
    When I type "n" in the search box
    Then I should see the following suggestions:
      | Dejan Angelov |
      | Reynold Kozey |

  Scenario: See a single suggestion
    Given I am on the members page
    When I type "Dej" in the search mox
    Then I should see the following suggestions:
      | Dejan Angelov |
    But I should not see the following suggestions:
      | Reynold Kozey |

  Scenario: See no suggestions
    Given I am on the members page
    When I type "uxz" in the search box
    Then I should see no suggestions

  Scenario: Click on a search suggestion
    Given I am on the members page
    When I type "Dejan" in the search box
    And I click on the "Dejan Angelov" suggestion
    Then I should be on "Dejan Angelov"'s profile page