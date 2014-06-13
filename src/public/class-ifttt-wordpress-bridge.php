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
		$options = get_option( 'ifttt_wordpress_bridge_options' );
		$this->log_enabled = $options && array_key_exists( 'log_enabled', $options ) && $options['log_enabled'] == true;
		$this->log( 'xmlrpc call received' );
		try {
			if ( $method != 'metaWeblog.newPost' ) {
				$this->log( "Method $method not relevant" );
				return;
			}
			$message = $this->create_message();
			$content_struct = $message->params[3];
			if ( ! $this->contains_ifttt_wordpress_bridge_tag( $content_struct ) ) {
				$this->log( "Tag 'ifttt_wordpress_bridge' not found" );
				return;
			}
			if ( $this->log_enabled ) {
				$content_struct_log  = "Received data:\n";
				$content_struct_log .= '  title: ' . $content_struct['title'] . "\n";
				$content_struct_log .= '  description: ' . $content_struct['description'] . "\n";
				$content_struct_log .= '  post_status: ' . $content_struct['post_status'] . "\n";
				$content_struct_log .= '  mt_keywords: ' . implode( ', ', $content_struct['mt_keywords'] );
				$this->log( $content_struct_log );
			}
			do_action( 'ifttt_wordpress_bridge', $content_struct );
			$this->log( "Successfully called 'ifttt_wordpress_bridge' actions" );
			header( 'Content-Type: text/xml; charset=UTF-8' );
			readfile( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'default_response.xml' );
			die();
		} catch (Exception $e) {
			$this->log( 'An error occurred: ' . $e->getMessage() );
		}
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
	 * Decides if the incoming request if relevant. A tag 'ifttt_wordpress_bridge' must be used.
	 *
	 * @since   1.0.0
	 */
	private function contains_ifttt_wordpress_bridge_tag( $content_struct ) {
		if ( ! array_key_exists( 'mt_keywords', $content_struct ) ) {
			return false;
		}
		$tags = $content_struct['mt_keywords'];
		foreach ( $tags as $tag ) {
			if ( $tag == 'ifttt_wordpress_bridge' ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Logs the given content to the log of this plugin.
	 *
	 * @since   1.0.0
	 */
	private function log( $content ) {
		if ( true !== $this->log_enabled ) {
			return;
		}
		$log_array = get_option( 'ifttt_wordpress_bridge_log' );
		if ( $log_array ) {
			if ( count( $log_array ) == 30 ) {
				array_pop( $log_array );
			}
			array_unshift( $log_array, $content );
			update_option( 'ifttt_wordpress_bridge_log', $log_array );
		} else {
			$log_array = array( $content );
			add_option( 'ifttt_wordpress_bridge_log', $log_array );
		}
	}	
}
