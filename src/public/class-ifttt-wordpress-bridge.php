<?php
/**
 * IFTTT WordPress Bridge
 *
 * @package   Ifttt_Wordpress_Bridge
 * @author    Björn Weinbrenner <info@bjoerne.com>
 * @license   GPLv3
 * @link      http://bjoerne.com
 * @copyright 2014 bjoerne.com
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @package Ifttt_Wordpress_Bridge
 * @author  Björn Weinbrenner <info@bjoerne.com>
 */
class Ifttt_Wordpress_Bridge {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $plugin_slug = 'ifttt-wordpress-bridge';

	/**
	 * Instance of this class.
	 *
	 * @since   1.0.0
	 *
	 * @var     object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since   1.0.0
	 */
	private function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'xmlrpc_call', array( $this, 'bridge' ) );
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since   1.0.0
	 *
	 * @return  Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since   1.0.0
	 *
	 * @return  object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since   1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Receives an incoming xmlrpc call, extract the payload data and performs 'ifttt_bridge' action.
	 *
	 * @since   1.0.0
	 */
	public function bridge( $method ) {
		if ( $method != 'metaWeblog.newPost' ) {
			return;
		}
		$message = $this->create_message();
		$contentStruct = $message->params[3];
		if ( ! $this->is_ifttt_bridge_call( $contentStruct ) ) {
			return;
		}
		do_action( 'ifttt_bridge', $this->parse_description( $contentStruct ) );
		header( 'Content-Type: text/xml; charset=UTF-8' );
		readfile( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'default_response.xml' );
		die();
	}

	/**
	 * Creates a IXR_Message from the incoming data.
	 *
	 * @since   1.0.0
	 */
	private function create_message() {
		global $HTTP_RAW_POST_DATA;
		if ( empty( $HTTP_RAW_POST_DATA ) ) {
			// workaround for a bug in PHP 5.2.2 - http://bugs.php.net/bug.php?id=41293
			$data = file_get_contents( 'php://input' );
		} else {
			$data =& $HTTP_RAW_POST_DATA;
		}
		$message = new IXR_Message( $data );
		$message->parse();
		return $message;
	}

	/**
	 * Decides if the incoming request if relevant. A tag 'IFTTT-Bridge' must be used.
	 *
	 * @since   1.0.0
	 */
	private function is_ifttt_bridge_call( $contentStruct ) {
		if ( ! array_key_exists( 'mt_keywords', $contentStruct ) ) {
			return false;
		}
		$tags = $contentStruct['mt_keywords'];
		foreach ( $tags as $tag ) {
			if ( $tag == 'IFTTT-Bridge' ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Parses the content. JSON format expected.
	 *
	 * @since   1.0.0
	 */
	private function parse_description( $content_struct ) {
		return json_decode( $content_struct['description'] );
	}
}
