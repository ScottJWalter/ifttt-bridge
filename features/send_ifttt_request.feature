Feature: Send request via IFTTT
  In order to use IFTTT in combination with WordPress
  As an administrator
  I need to be able to send requests via IFTTT
  
  Scenario: Response default xml
    Given a fresh WordPress is installed
    And the plugin "ifttt-wordpress-bridge" is installed (from source)
    And the plugin "ifttt-wordpress-bridge" is activated
    When I sent a request via IFTTT
    Then the response code should be 200
    And the response body should contain "<string>-1</string>"
    And the response should have content type "text/xml; charset=UTF-8"
