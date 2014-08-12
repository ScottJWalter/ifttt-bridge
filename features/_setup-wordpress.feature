@devonly
Feature: Setup fresh WordPress
  In order to update the database file
  As a behat developer
  I need to get a fresh WordPress

  Scenario: Get fresh WordPress
    Given a fresh WordPress is installed

  Scenario: Get WordPress with activated plugin
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated

  Scenario: Get German WordPress with activated plugin
    Given the blog language is "de_DE"
    And a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
