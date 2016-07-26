Feature: Configuration of QA tools
  In order to assure quality in my project
  As a developer
  I need to be able to configure the QA tools

  Scenario: Setting up the project's QA tools for the first time
    Given a project whose QA tools have not yet been set up
    And no available QA tools
    When I name the project "Philantropic Phantasia"
    And I want the QA-related files stored in "./"
    And I state the project type is "PHP"
    And I state the PHP project type is "Symfony 2"
    And I enable Travis
    Then I have a project configured accordingly
