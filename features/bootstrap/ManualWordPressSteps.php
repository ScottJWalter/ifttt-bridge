<?php

trait ManualWordPressSteps {

	/**
	 * @Given /^I am logged as an administrator$/
	 */
	public function login_as_administrator() {
		$this->login( 'admin', 'admin' );
	}

	/**
	 * @Given /^I logout$/
	 */
	public function logout() {
		$this->visit( 'wp-login.php?action=logout' );
		$this->get_page()->find( 'css', '#error-page a' )->click();
	}

	/**
	 * @Given /^I activate the plugin "([^"]*)"$/
	 */
	public function activate_plugin_manually( $plugin_name ) {
		$link = $this->get_plugin_area( $plugin_name )->find( 'xpath', "//a[contains(@href, 'action=activate')]" );
		assertNotNull( $link, 'Link not found' );
		$link->click();
	}

	/**
	 * @Given /^I deactivate the plugin "([^"]*)"$/
	 */
	public function deactivate_plugin_manually( $plugin_name ) {
		$link = $this->get_plugin_area( $plugin_name )->find( 'xpath', "//a[contains(@href, 'action=deactivate')]" );
		assertNotNull( $link, 'Link not found' );
		$link->click();
		assertNotNull( $this->get_page()->find( 'css', '.updated' ), "Can't find element" );
	}

	/**
	 * @Given /^I uninstall the plugin "([^"]*)"$/
	 */
	public function uninstall_plugin_manually( $plugin_name ) {
		$link = $this->get_plugin_area( $plugin_name )->find( 'xpath', "//a[contains(@href, 'action=delete-selected')]" );
		assertNotNull( $link, 'Link not found' );
		$link->click();
		$form   = $this->get_page()->find( 'xpath', "//form[contains(@action, 'action=delete-selected')]" );
		$submit = $form->find( 'css', '#submit' );
		assertNotNull( $submit );
		$submit->press();
		assertNotNull( $this->get_page()->find( 'css', '.updated' ), "Can't find element" );
	}

	private function get_plugin_area( $plugin_name ) {
		$plugin_area = $this->get_page()->find( 'xpath', "//tr[td/strong/text() = '$plugin_name']" );
		assertNotNull( $plugin_area, 'Plugin area not found' );
		return $plugin_area;
	}

	/**
	 * @Given /^I should see the message "([^"]*)"$/
	 */
	public function assert_message( $msg ) {
		assertNotNull( $this->get_page()->find( 'css', '.updated' ), "Can't find element" );
		assertTrue( $this->get_page()->hasContent( $msg ), "Can't find message" );
	}

	/**
	 * @Given /^I should see the error message "([^"]*)"$/
	 */
	public function assert_error_message( $msg ) {
		assertNotNull( $this->get_page()->find( 'css', '.error' ), "Can't find element" );
		assertTrue( $this->get_page()->hasContent( $msg ), "Can't find message" );
	}

	/**
	* @Given /^I should see$/
	*/
	public function assert_page_contains_all( $mixed ) {
		if ( is_a( $mixed, 'Behat\Gherkin\Node\TableNode' ) ) {
			$rows = $mixed->getRows();
			foreach ( $rows as $row ) {
				$this->assertPageContainsText( $row[0] );
			}
		} elseif ( is_a( $mixed, 'Behat\Gherkin\Node\PyStringNode' ) ) {
			foreach ( $mixed->getLines() as $line ) {
				$this->assertPageContainsText( trim( $line ) );
			}
		} else {
			throw new Exception( 'Unexpected type' );
		}
	}

	/**
	 * @Given /I should see an "([^"]*)" element having an attribute "([^"]*)" with value "([^"]*)"$/
	 */
	public function assert_element_having_attribute( $selector, $attribute_name, $attribute_value ) {
		$element = $this->get_page()->find( 'css', $selector );
		assertEquals( $attribute_value, $element->getAttribute( $attribute_name ) );
	}

	/**
	 * @Given /I should see an "([^"]*)" element not having an attribute "([^"]*)" with value "([^"]*)"$/
	 */
	public function assert_element_not_having_attribute( $selector, $attribute_name, $attribute_value ) {
		$element = $this->get_page()->find( 'css', $selector );
		assertNotEquals( $attribute_value, $element->getAttribute( $attribute_name ) );
	}

	/**
	 * @Given /^I wait for ([\d\.]*) second[s]?$/
	 */
	public function wait( $seconds ) {
		sleep( intval( $seconds ) );
	}

	/**
	 * Makes sure the current user is logged out, and then logs in with
	 * the given username and password.
	 *
	 * @param string $username
	 * @param string $password
	 * @author Maarten Jacobs
	 **/
	private function login( $username, $password ) {
		$this->visit( 'wp-admin' );
		$page = $this->get_page();
		for ( $i = 0; $i < 5; $i++ ) { 
			$page->fillField( 'user_login', $username );
			$page->fillField( 'user_pass', $password );
			if ( $this->getSession()->evaluateScript( "(function () { if (document.getElementById('user_pass').value == '') { return false; } else { document.getElementById('wp-submit').click(); return true; } })();" ) ) {
				break;
			}
		}
		assertTrue( $page->hasContent( 'Dashboard' ) );
	}

	private function get_page() {
		return $this->getSession()->getPage();
	}
}