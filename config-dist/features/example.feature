@frontend @database
Feature: Homepage
  In order to make profit
  the homepage must be up and running

  Scenario: User should see a contactform
    Given I am on "http://www.ibuildings.nl/"
    And I follow "Contact"
    Then I should see "Contactformulier"

  @mink:selenium2
  Scenario: The ibuildings block need to present on the php conference site 
    Given I am on "http://www.phpconference.nl/"
    Then I should see "ibuildings"

