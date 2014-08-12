Feature: Uninstall plugin
  In order to clean up
  As an administrator
  I need to be able to uninstall the plugin without a footprint

  Scenario: Uninstall plugin
    Given a fresh WordPress is installed
    And the plugin "ifttt-bridge" is installed (from src)
    And the plugin "ifttt-bridge" is activated
    And the option "ifttt_wordpress_bridge_options" has the serialized value { "log_level": "debug" }
    And the option "ifttt_wordpress_bridge_log" has the serialized value ["Some log content"]
    And I am logged as an administrator
    When I go to "/wp-admin/plugins.php"
    And I deactivate the plugin "IFTTT WordPress Bridge"
    And I uninstall the plugin "IFTTT WordPress Bridge"
    Then I should see the message "The selected plugins have been deleted"
    And the option "ifttt_wordpress_bridge_log" should not exist
    And the option "ifttt_wordpress_bridge_options" should not exist
