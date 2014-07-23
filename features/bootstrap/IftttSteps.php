<?php

trait IftttSteps {

	/**
	 * @Given /I sent a request via IFTTT$/
	 */
	public function sent_ifttt_request( $table = null ) {
		$xml_template = file_get_contents( dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'test_request_template.xml' );
		$defaults = array(
			'username' => 'admin',
			'password' => 'admin',
			'title' => 'title',
			'description' => 'description',
			'post_status' => 'draft',
			'categories' => '',
			'tags' => '',
		);
		$data = $table ? $table->getRowsHash() : array();
		$xml = $this->create_xml( $xml_template, array_merge( $defaults, $data ) );
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
		$xpath->query( '/methodCall/params/param[2]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['username'];
		$xpath->query( '/methodCall/params/param[3]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['password'];
		$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="title"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['title'];
		$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="description"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['description'];
		$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="post_status"]/value/string' )->item( 0 )->firstChild->nodeValue = $variables['post_status'];
		$categories = array_map( 'trim', explode( ',', $variables['categories'] ) );
		if ( ! empty( $categories ) && $categories[0] != '' ) {
			$categories_data = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]/value/array/data' )->item( 0 );
			$category_value  = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]/value/array/data/value', $categories_data )->item( 0 );
			for ( $i = 1; $i < count( $categories ); $i++ ) {
				$new_category_value = $category_value->cloneNode( true );
				$categories_data->appendChild( $new_category_value );
			}
			for ( $i = 0; $i < count( $categories ); $i++ ) { 
				$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]/value/array/data/value[' . ($i + 1) . ']/string' )->item( 0 )->firstChild->nodeValue = $categories[$i];
			}
		} else {
			$categories = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="categories"]' )->item( 0 );
			$categories->parentNode->removeChild( $categories );
		}
		$tags = array_map( 'trim', explode( ',', $variables['tags'] ) );
		if ( ! empty( $tags ) && $tags[0] != '' ) {
			array_unshift( $tags, 'ifttt_wordpress_bridge' );
		} else {
			$tags = array( 'ifttt_wordpress_bridge' );
		}
		$mt_keywords_data = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data' )->item( 0 );
		$tag_value = $xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data/value' )->item( 0 );
		for ( $i = 1; $i < count( $tags ); $i++ ) {
			$new_tag_value = $tag_value->cloneNode( true );
			$mt_keywords_data->appendChild( $new_tag_value );
		}
		for ( $i = 0; $i < count( $tags ); $i++ ) { 
			$xpath->query( '/methodCall/params/param[4]/value/struct/member[name="mt_keywords"]/value/array/data/value[' . ($i + 1) . ']/string' )->item( 0 )->firstChild->nodeValue = $tags[$i];
		}
		return $doc->saveXML();
	}
}