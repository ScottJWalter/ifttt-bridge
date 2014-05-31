Feature: Log the processing of the IFTTT xmlrcp call
  In order see what happens during a IFTTT xmlrcp call
  As an administrator
  I need to be able to see a informative log

  Scenario: Log IFTTT request
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    When I sent a request via IFTTT
    Then the log contains
      | Successfully called 'ifttt_wordpress_bridge' actions |
      | Bridge data: %DESCRIPTION% |
      | Raw data: %DESCRIPTION% |
      | xmlrpc call received |

  Scenario: Log exception
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the plugin "ifttt-wordpress-bridge-testplugin" is installed (from features/plugins/ifttt-wordpress-bridge-testplugin.php)
    And the plugin "ifttt-wordpress-bridge-testplugin" is activated
    And the option "ifttt_wordpress_bridge_testplugin_scenario" has the value "throw_exception"
    When I sent a request via IFTTT
    Then the log contains
      | An error occurred: Error processing ifttt_wordpress_bridge action |
      | Bridge data: %DESCRIPTION% |
      | Raw data: %DESCRIPTION% |
      | xmlrpc call received |

  Scenario: See empty log
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    Then the "ifttt-wordpress-bridge-log" field should contain ""

  Scenario: See log
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And I sent a request via IFTTT
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    Then I should see
      | Successfully called 'ifttt_wordpress_bridge' actions |
      | Bridge data: %DESCRIPTION% |
      | Raw data: %DESCRIPTION% |
      | xmlrpc call received |
