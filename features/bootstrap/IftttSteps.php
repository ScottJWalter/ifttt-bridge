<?php

trait IftttSteps {

	/**
	 * @Given /I sent a request via IFTTT$/
	 */
	public function sent_ifttt_request( $table = null ) {
		$xml_template = file_get_contents( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'ifttt_request_template.xml' );
		if ( $table ) {
			$rows_hash = $table->getRowsHash();
			$xml = $this->create_xml( $xml_template, $rows_hash );
		} else {
			$xml = $xml_template;
		}
		$curl_handle = curl_init( $this->parameters['webserver_url'] . '/xmlrpc.php' );
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

	/**
	 * Creates an xml from a template by replacing placeholders and duplicating nodes if necessary.
	 */
	private function create_xml( $xml_template, $variables ) {
		$doc = new DOMDocument();
		$doc->loadXML( $xml_template );
		$xpath = new DOMXPath( $doc );
		if ( array_key_exists( 'title' , $variables ) ) {
			$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="title"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['title'];
		}
		if ( array_key_exists( 'description' , $variables ) ) {
			$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="description"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['description'];
		}
		if ( array_key_exists( 'post_status' , $variables ) ) {
			$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="post_status"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['post_status'];
		}
		if ( array_key_exists( 'tags' , $variables ) ) {
			$mt_keywords_data = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data' )->item( 0 );
			$tag_value = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data/value' )->item( 0 );
			$tags = array_map( 'trim', explode( ',', $variables['tags'] ) );
			foreach ( $tags as $tag ) {
				$new_tag_value = $tag_value->cloneNode( true );
				$new_tag_value->nodeValue = $tag;
				$mt_keywords_data->appendChild( $new_tag_value );
			}
		}
		return $doc->saveXML();
	}
}