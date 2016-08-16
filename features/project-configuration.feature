Feature: Project configuration
  In order to assure quality in my project
  As a developer
  I need to be able to configure the project

  Scenario: Setting up the project's QA tools for the first time
    Given a project whose QA tools have not yet been set up
    And no available QA tools
    When I name the project "Trading Service"
    And I want the QA-related files stored in "./"
    And I state the project type is "PHP"
    And I state the PHP project type is "Symfony 2"
    And I disable Travis
    Then I have a project configured accordingly

  Scenario: Adjusting the storage location
    Given the Trading Service project
    And no available QA tools
    When I want the QA-related files stored in "./qa"
    Then I have a project configured accordingly
