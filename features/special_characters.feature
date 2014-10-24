Feature: Handle special characters correctly
  In order see process and display received data
  As an administrator
  I need to get/view the original data even if it contains special characters

  Scenario: Log complete request
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_bridge_options" has the serialized value { "log_level": "debug" }
    When I sent a request via IFTTT
      | title       | "An Instagram image with <special characters> & umläüte" |
      | description | And this is a description |
      | post_status | draft                     |
    And I am logged as an administrator
    And I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    Then I should see
      | <string>"An Instagram image with &lt;special characters&gt; &amp; umläüte"</string> |

  Scenario: See request summary
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_bridge_options" has the serialized value { "log_level": "info" }
    And I sent a request via IFTTT
      | title       | "An Instagram image with <special characters> & umläüte" |
      | description | And this is a description |
      | post_status | draft |
    And I am logged as an administrator
    When I go to "/wp-admin/options-general.php?page=ifttt-bridge.php"
    Then I should see
      | Successfully called 'ifttt_bridge' actions |
      | xmlrpc call received |
    And I should see
      """
      Received data:
        title: "An Instagram image with <special characters> & umläüte"
        description: And this is a description
        post_status: draft
        mt_keywords: ifttt_bridge
      """
