Feature: Check if working

  Scenario: Log in
    Given I am on the homepage
    And I fill in the following:
      | email | admin@ultim8.info |
      | password | 123456         |
    And I press "Sign in"
    Then I should see "New members per month"

  @javascript
  Scenario: Seeing a modal window
    Given I am logged in as a board member
    And I am on the "documents" path
    And I press "Add document"
    And I wait for the modal window to open
    And I fill in the following:
      | document-title       | Something bla bla |
      | document-description | Some funny text   |
    And I press "Close"
    Then the modal window should disappear
