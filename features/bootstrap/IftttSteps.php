<?php

trait IftttSteps {

	/**
	 * @Given /I sent a request via IFTTT$/
	 */
	public function set_ifttt_request() {
		$url = $this->parameters['webserver_url'] . '/xmlrpc.php';
		$xml = file_get_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'ifttt_request.xml' );
		$curl_handle = curl_init( $url );
		curl_setopt( $curl_handle, CURLOPT_POST, 1 );
		curl_setopt( $curl_handle, CURLOPT_POSTFIELDS, $xml );
		curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, true );
		$this->response_body = curl_exec( $curl_handle );
		$this->response_info = curl_getinfo( $curl_handle );
		curl_close( $curl_handle );
	}

	/**
	 * @Given /the response code should be (\d*)$/
	 */
	public function assert_response_code( $expected ) {
		assertEquals( $expected, $this->response_info['http_code'] );
	}

	/**
	 * @Given /the response body should contain "([^"]*)"$/
	 */
	public function assert_response_body_contains( $contained ) {
		assertTrue( strpos( $this->response_body, $contained ) !== false, "Response body $this->response_body doesn't contain $contained" );
	}

	/**
	 * @Given /the response should have content type "([^"]*)"$/
	 */
	public function assert_response_content_type( $expected ) {
		assertEquals( $expected, $this->response_info['content_type'] );
	}
}