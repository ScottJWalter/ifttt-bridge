Feature: Log the processing of the IFTTT xmlrcp call
  In order see what happens during a IFTTT xmlrcp call
  As an administrator
  I need to be able to see a informative log

  Scenario: Log IFTTT request
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_enabled": true }
    When I sent a request via IFTTT
      | title       | This is a title |
      | description | And this is a description |
      | post_status | draft |
      | tags | foo, bar |
    Then the log contains
      | Successfully called 'ifttt_wordpress_bridge' actions |
      | Received data: {"title":"This is a title","description":"And this is a description","post_status":"draft","mt_keywords":["ifttt_wordpress_bridge","foo","bar"]} |
      | xmlrpc call received |

  Scenario: Don't log IFTTT request if log is disabled
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    When I sent a request via IFTTT
    Then the option "ifttt_wordpress_bridge_log" should not exist

  Scenario: Log exception
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the plugin "ifttt-wordpress-bridge-testplugin" is installed (from features/plugins/ifttt-wordpress-bridge-testplugin.php)
    And the plugin "ifttt-wordpress-bridge-testplugin" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_enabled": true }
    And the option "ifttt_wordpress_bridge_testplugin_scenario" has the value "throw_exception"
    When I sent a request via IFTTT
      | title       | This is a title |
      | description | And this is a description |
      | post_status | draft |
    Then the log contains
      | An error occurred: Error processing ifttt_wordpress_bridge action |
      | Received data: {"title":"This is a title","description":"And this is a description","post_status":"draft","mt_keywords":["ifttt_wordpress_bridge"]} |
      | xmlrpc call received |

  Scenario: See empty log
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_enabled": true }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    Then the "ifttt-wordpress-bridge-log" field should contain ""

  Scenario: See log
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_enabled": true }
    And I sent a request via IFTTT
      | title       | This is a title |
      | description | And this is a description |
      | post_status | draft |
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    Then I should see
      | Successfully called 'ifttt_wordpress_bridge' actions |
      | Received data: {"title":"This is a title","description":"And this is a description","post_status":"draft","mt_keywords":["ifttt_wordpress_bridge"]} |
      | xmlrpc call received |

  Scenario: Log disabled on admin page
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    Then the checkbox "ifttt_wordpress_bridge_options_log_enabled" should not be checked

  Scenario: Log enabled on admin page
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_enabled": true }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    Then the checkbox "ifttt_wordpress_bridge_options_log_enabled" should be checked

  Scenario: Enable log
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    And I check "ifttt_wordpress_bridge_options_log_enabled"
    And I press "submit"
    Then I should see the message "Settings saved"
    And I should see an "#ifttt-wordpress-bridge-log" element not having an attribute "disabled" with value "disabled"

  Scenario: Disable log
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_enabled": true }
    And I sent a request via IFTTT
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    And I uncheck "ifttt_wordpress_bridge_options_log_enabled"
    And I press "submit"
    Then I should see the message "Settings saved"
    And I should see an "#ifttt-wordpress-bridge-log" element having an attribute "disabled" with value "disabled"
    And the "ifttt-wordpress-bridge-log" field should contain ""
