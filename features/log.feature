Feature: Log the processing of the IFTTT xmlrcp call
  In order see what happens during a IFTTT xmlrcp call
  As an administrator
  I need to be able to see a informative log

  Scenario: Log IFTTT request
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "info" }
    When I sent a request via IFTTT
      | title       | This is a title           |
      | description | And this is a description |
      | post_status | draft                     |
      | tags        | foo, bar                  |
    Then the log contains 3 entries
    Then the log contains "Successfully called 'ifttt_wordpress_bridge' actions"
    Then the log contains "xmlrpc call received"
    Then the log contains
      """
      Received data:
        title: This is a title
        description: And this is a description
        post_status: draft
        categories: 
        mt_keywords: ifttt_wordpress_bridge, foo, bar
      """

  Scenario: Don't log IFTTT request if log is disabled
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    When I sent a request via IFTTT
    Then the option "ifttt_wordpress_bridge_log" should not exist

  Scenario: Log exception
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the plugin "ifttt-bridge-testplugin" is installed (from features/plugins/ifttt-bridge-testplugin.php)
    And the plugin "ifttt-bridge-testplugin" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "error" }
    And the option "ifttt_wordpress_bridge_testplugin_scenario" has the value "throw_exception"
    When I sent a request via IFTTT
      | title       | This is a title |
      | description | And this is a description |
      | post_status | draft |
    Then the log contains 1 entries
    Then the log contains "An error occurred: Error processing ifttt_wordpress_bridge action"

  Scenario: See empty log message
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "info" }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    Then I should see an ".no-log-messages" element

  Scenario: See log
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "info" }
    And I sent a request via IFTTT
      | title       | This is a title |
      | description | And this is a description |
      | post_status | draft |
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    Then I should see
      | Successfully called 'ifttt_wordpress_bridge' actions |
      | xmlrpc call received |
    And I should see
      """
      Received data:
        title: This is a title
        description: And this is a description
        post_status: draft
        mt_keywords: ifttt_wordpress_bridge
      """

  Scenario: Log disabled on admin page
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    Then the "ifttt_wordpress_bridge_options_log_level" field should contain "off"

  Scenario: Log enabled on admin page
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "info" }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    Then the "ifttt_wordpress_bridge_options_log_level" field should contain "info"

  Scenario: Enable log
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    And I select "info" from "ifttt_wordpress_bridge_options_log_level"
    And I press "submit"
    Then I should see the message "Settings saved"
    Then the "ifttt_wordpress_bridge_options_log_level" field should contain "info"

  Scenario: Disable log
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "info" }
    And I sent a request via IFTTT
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    And I select "off" from "ifttt_wordpress_bridge_options_log_level"
    And I press "submit"
    Then I should see the message "Settings saved"
    And I should see an ".no-log-messages" element

  Scenario: Increase log level
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "debug" }
    When I sent a request via IFTTT
      | title       | This is a title           |
      | description | And this is a description |
      | post_status | draft                     |
      | tags        | foo, bar                  |
    Then the log contains 4 entries
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    And I select "info" from "ifttt_wordpress_bridge_options_log_level"
    And I press "submit"
    Then I should see the message "Settings saved"
    And the log contains 3 entries
