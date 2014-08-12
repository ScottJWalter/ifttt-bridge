<?php
/**
 * @package Ifttt_Bridge_Testplugin
 * @version 0.0.0
 */
/*
Plugin Name: IFTTT Bridge for WordPress Testplugin
Version: 0.0.0
*/

function ifttt_bridge_testplugin_action( $content_struct ) {
	$scenario = get_option( 'ifttt_bridge_testplugin_scenario' );
	if ( 'throw_exception' == $scenario ) {
		throw new Exception( 'Error processing ifttt_bridge action' );
	} elseif ( 'add_option' == $scenario ) {
		add_option( 'ifttt_bridge_testplugin_option' , $content_struct );
	}
}

add_action( 'ifttt_bridge', 'ifttt_bridge_testplugin_action' );
