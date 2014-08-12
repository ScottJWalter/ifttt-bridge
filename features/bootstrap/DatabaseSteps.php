<?php

trait DatabaseSteps {

	/**
	 * @Given /^the plugin "([^"]*)" is activated$/
	 */
	public function activate_plugin( $plugin_id ) {
		if ( file_exists( $this->path( $this->webserver_dir, 'wp-content', 'plugins', $plugin_file = "$plugin_id.php" ) ) ) {
		} elseif ( file_exists( $this->path( $this->webserver_dir, 'wp-content', 'plugins', $plugin_file = "$plugin_id/$plugin_id.php" ) ) ) {
		} else {
			throw new Exception( "Plugin file '$plugin_id' not found" );			
		}
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => 'active_plugins' ) );
		$option_value = $stmt->fetch( PDO::FETCH_ASSOC )['option_value'];
		$unserialized = unserialize( $option_value );
		foreach ( $unserialized as $active_plugin ) {
			if ( $active_plugin == $plugin_file ) {
				return;
			}
		}
		$unserialized[] = $plugin_file;
		$option_value   = serialize( $unserialized );
		$stmt = $pdo->prepare( 'UPDATE wp_options SET option_value = :option_value WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => 'active_plugins', ':option_value' => $option_value ) );
	}

	/**
	 * @Given /^the option "([^"]*)" has the serialized value ([^']*)$/
	 */
	public function set_serialized_option( $option_name, $option_value ) {
		$option_value_obj = json_decode( $option_value, true );
		$serialized = serialize( $option_value_obj );
		$this->set_option( $option_name, $serialized );
	}

	/**
	 * @Given /^the option "([^"]*)" has the value "([^"]*)"$/
	 */
	public function set_option( $option_name, $option_value ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name AND option_value = :option_value' );
		$stmt->execute( array( ':option_name' => $option_name, ':option_value' => $option_value ) );
		$result = $this->fetch_all( $stmt );
		if ( 0 == count( $result ) ) {
			$stmt = $pdo->prepare( 'INSERT INTO wp_options (option_name, option_value) VALUES (:option_name, :option_value)' );
		} else {
			$stmt = $pdo->prepare( 'UPDATE wp_options SET option_value = :option_value WHERE option_name = :option_name' );
		}
		$stmt->execute( array( ':option_name' => $option_name, ':option_value' => $option_value ) );
	}

	/**
	 * @Given /the option "([^"]*)" should have the serialized value (.*)$/
	 */
	public function assert_serialized_option_value( $option_name, $option_value ) {
		$option_value_obj = json_decode( $option_value, true );
		$serialized = serialize( $option_value_obj );
		$this->assert_option_value( $option_name, $serialized );
	}

	/**
	 * @Given /the option "([^"]*)" should have the value "([^"]*)"$/
	 */
	public function assert_option_value( $option_name, $option_value ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => $option_name ) );
		$result = $this->fetch_all( $stmt );
		assertEquals( count( $result ), 1, "Option '$option_name' doesn't exists" );
		assertEquals( $option_value, $result[0]['option_value'], "Option '$option_name' should have value '$option_value' but has value '".$result[0]['option_value']."'" );
	}

	/**
	 * @Given /^the option "([^"]*)" should not exist$/
	 */
	public function assert_option_not_exists( $option_name ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => $option_name ) );
		$result = $this->fetch_all( $stmt );
		assertEquals( count( $result ), 0, "The option '$option_name' was found but should not exist" );
	}

	/**
	 * @Given /the log contains (\d*) entries$/
	 */
	public function assert_num_of_log_entries( $num_of_log_entries ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => 'ifttt_bridge_log' ) );
		$result = $this->fetch_all( $stmt );
		assertEquals( count( $result ), 1, "Option 'ifttt_bridge_log' doesn't exists" );
		$log_entries = unserialize( $result[0]['option_value'] );
		assertEquals( $num_of_log_entries, count( $log_entries ) );
	}

	/**
	 * @Given /the log contains "([^"]*)"$/
	 * @Given /the log contains$/
	 */
	public function assert_log( $expected_log_entry_msg ) {
		$pdo  = $this->create_pdo();
		$stmt = $pdo->prepare( 'SELECT * FROM wp_options WHERE option_name = :option_name' );
		$stmt->execute( array( ':option_name' => 'ifttt_bridge_log' ) );
		$result = $this->fetch_all( $stmt );
		assertEquals( count( $result ), 1, "Option 'ifttt_bridge_log' doesn't exists" );
		$log_entries = unserialize( $result[0]['option_value'] );
		for ( $i = 0, $count_log_entries = count( $log_entries ); $i < $count_log_entries; $i++ ) {
			if ( $expected_log_entry_msg == $log_entries[$i]['message'] ) {
				return;
			}
		}
		PHPUnit_Framework_Assert::fail( "Log entry '" . $expected_log_entry_msg . "' not found in log " . json_encode( $log_entries ) );
	}

	private function create_pdo() {
		$pdo = new PDO( 'sqlite:'.$this->path( $this->temp_dir, $this->database_file ) );
		$pdo->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		return $pdo;
	}

	private function fetch_all( $stmt ) {
		$result = array();
		while ( $row = $stmt->fetch( PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT ) )  {
			$result[] = $row;
		}
		return $result;
	}
}