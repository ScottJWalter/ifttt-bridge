Feature: Send test requests
  In order simulate IFTTT for test purposes
  As an administrator
  I need to be able to send test requests

  Scenario: Send test request
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_enabled": true }
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-wordpress-bridge.php"
    And I fill in "test-request-title" with "This is a title"
    And I fill in "test-request-description" with "And this is a description"
    And I fill in "test-request-tags" with "foo, bar"
    And I check "test-request-draft"
    And I press "send-test-request"
    Then I should see "Test request sent"
    And the log contains
      | Successfully called 'ifttt_wordpress_bridge' actions |
      | Received data: {"title":"This is a title","description":"And this is a description","post_status":"draft","mt_keywords":["ifttt_wordpress_bridge","foo","bar"]} |
      | xmlrpc call received |

