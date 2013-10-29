@frontend @database
Feature: Homepage
  In order to make profit
  the homepage must be up and running

  Scenario: User should see a contactform
    Given I am on "http://www.ibuildings.nl/"
    And I follow "Contact"
    Then I should see "Contactformulier"
