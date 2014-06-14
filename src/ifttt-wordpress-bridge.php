<?php
/**
 * @package   Ifttt_Wordpress_Bridge
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 *
 * @wordpress-plugin
 * Plugin Name:       IFTTT WordPress Bridge
 * Plugin URI:        http://www.bjoerne.com
 * Description:       When using IFTTT with WordPress you can just post new posts. This plugin helps you to do whatever you like. Configure your IFTTT job in a predefined way in you get any information you like in a WordPress action.
 * Version:           0.9.0
 * Author:            Björn Weinbrenner
 * Author URI:        http://www.bjoerne.com/
 * Text Domain:       ifttt-wordpress-bridge
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/bjoerne2/ifttt-wordpress-bridge
 * WordPress-Plugin-Boilerplate: v2.6.1
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once( plugin_dir_path( __FILE__ ) . 'public/class-ifttt-wordpress-bridge.php' );

add_action( 'plugins_loaded', array( 'Ifttt_Wordpress_Bridge', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-ifttt-wordpress-bridge-admin.php' );
	add_action( 'plugins_loaded', array( 'Ifttt_Wordpress_Bridge_Admin', 'get_instance' ) );
}
