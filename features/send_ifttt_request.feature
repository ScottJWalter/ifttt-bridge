Feature: Send request via IFTTT
  In order to use IFTTT in combination with WordPress
  As an administrator
  I need to be able to send requests via IFTTT
  
  Scenario: Response default xml
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    When I sent a request via IFTTT
    Then the response code should be 200
    And the response body should contain "<string>-1</string>"
    And the response should have content type "text/xml; charset=UTF-8"
  
  Scenario: Perform action
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from src)
    And the plugin "ifttt-wordpress-bridge" is activated
    And the plugin "ifttt-wordpress-bridge-testplugin" is installed (from features/plugins/ifttt-wordpress-bridge-testplugin.php)
    And the plugin "ifttt-wordpress-bridge-testplugin" is activated
    And the option "ifttt_wordpress_bridge_testplugin_scenario" has the value "add_option"
    When I sent a request via IFTTT
      | title       | IFTTT test        |
      | description | It's IFTTT, dude! |
      | post_status | draft             |
      | categories  | foo, bar          |
      | tags        | another_tag       |
    Then the option "ifttt_wordpress_bridge_testplugin_option" should have the serialized value {"title":"IFTTT test","description":"It's IFTTT, dude!","post_status":"draft","categories":["foo","bar"],"mt_keywords":["ifttt_wordpress_bridge","another_tag"]}
