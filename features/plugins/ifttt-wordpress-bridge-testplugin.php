<?php
/**
 * @package Ifttt_Wordpress_Bridge_Testplugin
 * @version 0.0.0
 */
/*
Plugin Name: IFTTT WordPress Bridge Testplugin
Version: 0.0.0
*/

function ifttt_wordpress_bridge_testplugin_action( $content_struct ) {
	$scenario = get_option( 'ifttt_wordpress_bridge_testplugin_scenario' );
	if ( 'throw_exception' == $scenario ) {
		throw new Exception( 'Error processing ifttt_wordpress_bridge action' );
	} elseif ( 'add_option' == $scenario ) {
		add_option( 'ifttt_wordpress_bridge_testplugin_option' , $content_struct['title'] );
	}
}

add_action( 'ifttt_wordpress_bridge', 'ifttt_wordpress_bridge_testplugin_action' );
