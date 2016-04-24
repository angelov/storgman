@members @database
Feature: Editing any member's profile
  In order manage the information submitted by a member
  As a board member
  I should be able to edit any member's profile

  Background:
    Given there are the following members:
      | first_name | last_name  | birthday   | faculty | field            | graduation_year | email               | password | board | position      | approved | alumni |
      | Dejan      | Angelov    | 1992-01-01 | FCSE    | Computer Science | 2016            | angelov@example.org | 123456   | yes   | Administrator | yes      | no     |
      | Reynold    | Kozey      | 1990-01-01 | FCSE    | Networking       | 2016            | reynold@example.org | 123456   | no    |               | yes      | yes    |
    And I am logged in as "Dejan Angelov"

  Scenario: Accessing the edit page from members list
    Given I am on the members page
    When I click on the edit icon near "Reynold Kozey"
    Then I should see "Edit member details"

  Scenario: Accessing the edit page from member's profile page
    Given I am on "Reynold Kozey"'s profile page
    And I click on "Edit details"
    Then I should see "Edit member details"

  Scenario: Seeing the member's existing information
    Given I am editing "Reynold Kozey"'s profile
    Then I should see the following infomation:
      | First Name                     | Reynold             |
      | Last Name                      | Kozey               |
      | Birthday                       | 1990-01-01          |
      | Email                          | reynold@example.org |
      | Password                       |                     |
      | Faculty                        | FCSE                |
      | Field of study                 | Networking          |
      | (Expected) year of graduation: | 2016                |

  Scenario: Editing a board member's information
    Given I am editing "Dejan Angelov"'s profile
    Then the "Board member" field should be checked
    And the "Board member" field should contain "Administrator"

  Scenario: Editing an alumni member's information
    Given I am editing "Reynold Kozey"'s profile
    Then the "Alumni member" field should be checked

  Scenario: Submitting the form without any modifications
    Given I am editing "Reynold Kozey"'s profile
    When I press "Update details"
    Then I should be on the members page
    And I should see "Member updated successfully."

  Scenario: Submitting the complete form with valid information:
    Given I am editing "Reynold Kozey"'s profile
    When I fill in the following:
      | Website:      | http://angelovdejan.me |
      | Phone number: | +38971234567           |
      | Facebook:     | angelovdejan           |
      | Twitter:      | angelovdejan           |
      | Google+:      | angelovdejan           |
    # @todo: Fill the photo field
    And I press "Update details"
    Then I should be on the members page
    And I should see "Member updated successfully."

  Scenario Outline: Trying to save without some of the required information
    When I fill in the following:
      | <field> | |
    And I press "Update"
    Then I should see "Please fix the following errors:"
    And I should see "<message>"

    Examples:
      | field                          | message |
      | First name:                    | The first name field is required.         |
      | Last name:                     | The last name field is required.          |
      | Birthday:                      | The birthday field is required.           |
      | Email:                         | The email field is required.              |
      | Password:                      | The password field is required.           |
      | Faculty:                       | The faculty field is required.            |
      | Field of study:                | The field of study field is required.     |
      | (Expected) year of graduation: | The year of graduation field is required. |