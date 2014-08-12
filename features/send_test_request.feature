Feature: Send test requests
  In order simulate IFTTT for test purposes
  As an administrator
  I need to be able to send test requests

  Scenario: Send simple test request
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_bridge_options" has the serialized value { "log_level": "info" }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    And I fill in the following:
      | test-request-username   | admin                     |
      | test-request-password   | admin                     |
      | test-request-title      | This is a title           |
      | test-request-body       | And this is a description |
      | test-request-tags       | foo, bar                  |
    And I check "test-request-draft"
    And I press "send-test-request"
    Then I should see "Test request sent"
    And the log contains "Successfully called 'ifttt_bridge' actions"
    And the log contains
      """
      Received data:
        title: This is a title
        description: And this is a description
        post_status: draft
        categories: 
        mt_keywords: ifttt_bridge, foo, bar
      """
    And the log contains "xmlrpc call received"

  Scenario: Send quoted test request
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_bridge_options" has the serialized value { "log_level": "info" }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    And I fill in the following:
      | test-request-username   | admin                     |
      | test-request-password   | admin                     |
      | test-request-title      | This is a "quoted" title  |
      | test-request-body       | And this is a description |
      | test-request-tags       | foo, bar                  |
    And I check "test-request-draft"
    And I press "send-test-request"
    Then I should see "Test request sent"
    And the log contains "Successfully called 'ifttt_bridge' actions"
    And the log contains
      """
      Received data:
        title: This is a "quoted" title
        description: And this is a description
        post_status: draft
        categories: 
        mt_keywords: ifttt_bridge, foo, bar
      """
    And the log contains "xmlrpc call received"


  Scenario: Send categories
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_bridge_options" has the serialized value { "log_level": "info" }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    And I fill in the following:
      | test-request-username   | admin                     |
      | test-request-password   | admin                     |
      | test-request-title      | This is a title           |
      | test-request-body       | And this is a description |
      | test-request-categories | foo, bar                  |
    And I check "test-request-draft"
    And I press "send-test-request"
    Then I should see "Test request sent"
    And the log contains "Successfully called 'ifttt_bridge' actions"
    And the log contains
      """
      Received data:
        title: This is a title
        description: And this is a description
        post_status: draft
        categories: foo, bar
        mt_keywords: ifttt_bridge
      """
    And the log contains "xmlrpc call received"
