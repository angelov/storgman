@members @database
Feature: Registration
  In order to use the application
  As a new organization member
  I need to create a new account

  Background:
    Given there are the following members:
      | first_name | last_name  | email               | password | board | approved |
      | Dejan      | Angelov    | angelov@example.org | 123456   | yes   | yes      |
      | Reynold    | Kozey      | reynold@example.org | 123456   | no    | yes      |
    And I am on the registration page
    And I fill in the following:
      | First name:                    | Christiana        |
      | Last name:                     | Sporer            |
      | Birthday:                      | 1992-02-23        |
      | Email:                         | valid@example.com |
      | Password:                      | 123456            |
      | Faculty:                       | FCSE              |
      | Field of study:                | Computer Science  |
      | (Expected) year of graduation: | 2017              |

  Scenario: Try to open the registration page while logged in as board member
    Given I am logged in as board member
    When I go to the registration page
    Then I should be on the homepage

  Scenario: Try to open the registration page while logged in as regular member
    Given I am logged in as "Reynold Kozey"
    When I go to the registration page
    Then I should be on my profile page

  Scenario: Opening the registration page
    Given I am on the login page
    And I follow "Create account"
    Then I should be on the registration page
    And I should see "Create new account:"

  Scenario: Submitting the form with the minimal valid information:
    When I press "Register"
    Then I should see "Your account was created successfully. You will be notified when the board members approve it."

  Scenario: Submitting the complete form with valid information:
    Given I fill in the following:
      | Website:      | http://angelovdejan.me |
      | Phone number: | +38971234567           |
      | Facebook:     | angelovdejan           |
      | Twitter:      | angelovdejan           |
      | Google+:      | angelovdejan           |
      # @todo: Fill the photo field
    When I press "Register"
    Then I should see "Your account was created successfully. You will be notified when the board members approve it."

  Scenario Outline: Trying to register without the required information:
    When I fill in the following:
      | <field> | |
    And I press "Register"
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

  Scenario Outline: Try to register with invalid information
    When I fill in the following:
      | <field> | <data> |
    And I press "Register"
    Then I should see "Please fix the following errors:"
    And I should see "<message>"

    Examples:
      | field                          | data          | message                                             |
      | Email:                         | invalid-email | Please enter a valid email address.                 |
      | Email:                         | dejan@gmail   | Please enter a valid email address.                 |
      | Password:                      | 1234          | The password must be at least 6 characters.         |
      | (Expected) year of graduation: | asd           | Please enter a valid (expected) year of graduation. |
      | Birthday:                      | 123           | Please enter a valid date for your birthday.        |
      | Website:                       | asdasd        | Please enter a valid URL for your website.          |

  Scenario: Try to register with existing email
    When I fill in "Email:" with "angelov@example.org"
    And I press "Register"
    Then I should see "Please fix the following errors:"
    And I should see "The email has already been taken."