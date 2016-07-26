Feature: Configuration of QA tools
  In order to assure quality in my project
  As a developer
  I need to be able to configure the QA tools

  Scenario: Setting up the project's QA tools for the first time
    Given a project whose QA tools have not yet been set up
    And no available QA tools
    When I name the project "Trading Service"
    And I want the QA-related files stored in "./"
    And I state the project type is "PHP"
    And I state the PHP project type is "Symfony 2"
    And I enable Travis
    Then I have a project configured accordingly

  @wip
  Scenario: Adjusting the storage location
    Given the Trading Service project
    And no available QA tools
    When I keep the project name
    And I keep the project type
    And I keep the PHP project type
    And I keep Travis enabled
    But I want the QA-related files stored in "./qa"
    Then I have a project configured accordingly
