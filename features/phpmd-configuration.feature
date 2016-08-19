Feature: PHPMD configuration
  In order to assure I don't make a mess of my PHP code
  As a developer
  I need to be able to configure PHPMD

  Scenario: Installing PHPMD
    Given the Trading Service project
    And the PHPMD Symfony 2 configurator is available
    When I want to use PHPMD
    And the configuration is complete
    Then the PHPMD Composer package is installed

  Scenario: Skipping install of PHPMD
    Given the Trading Service project
    And the PHPMD Symfony 2 configurator is available
    When I don't want to use PHPMD
    And the configuration is complete
    Then the PHPMD Composer package is not installed
