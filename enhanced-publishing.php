<?php
/**
 * Plugin Name: Enhanced Publishing
 * Plugin URI: https://www.echoplugins.com
 * Description: Create and publish your posts and pages on WordPress faster and with less frustration
 * Version: 3.0.0
 * Author: Echo Plugins
 * Author URI: https://www.echoplugins.com
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Enhanced Publishing is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Enhanced Publishing is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Enhanced Publishing. If not, see <http://www.gnu.org/licenses/>.
 *
*/

/* Adapted from code in EDD (Copyright (c) 2015, Pippin Williamson) and WP. */

if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Main class to load the plugin.
 *
 * Singleton
 */
final class Enhanced_Publishing {

	/* @var Enhanced_Publishing */
	private static $instance;

	public static $version = '3.0.0';
	public static $plugin_dir;
	public static $plugin_url;
	public static $plugin_file = __FILE__;

	/* @var EPUB_Settings_DB */
	public $settings_obj;

	/**
	 * Initialise the plugin
	 */
	private function __construct() {
		self::$plugin_dir = plugin_dir_path(  __FILE__ );
		self::$plugin_url = plugin_dir_url( __FILE__ );
	}

	/**
	 * Retrieve or create a new instance of this main class (avoid global vars)
	 *
	 * @static
	 * @return Enhanced_Publishing
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Enhanced_Publishing ) ) {
			self::$instance = new Enhanced_Publishing();

			self::$instance->setup_system();
			self::$instance->setup_plugin();

			add_action( 'plugins_loaded', array( self::$instance, 'load_text_domain' ) );
			add_action( 'init', array( self::$instance, 'epub_stop_heartbeat' ), 1 );
		}
		return self::$instance;
	}

	/**
	 * Setup class auto-loading and other support functions. Setup custom core features.
	 */
	private function setup_system() {

		// autoload classes ONLY when needed by executed code rather than on every page request
		require_once self::$plugin_dir . 'includes/system/class-epub-autoloader.php';

		// load non-classes
		require_once self::$plugin_dir . 'includes/functions.php';
		require_once self::$plugin_dir . 'includes/system/plugin-setup.php';
		require_once self::$plugin_dir . 'includes/system/scripts-registration.php';
		require_once self::$plugin_dir . 'includes/system/plugin-links.php';

		// register settings
		self::$instance->settings_obj = new EPUB_Settings_DB();
	}

	/**
	 * Setup plugin before it runs. Include functions and instantiate classes based on user action
	 */
	private function setup_plugin() {

		// process action request if any
		if ( isset($_REQUEST['action']) ) {
			$this->handle_action_request();
		}

		// handle AJAX front & back-end requests (no admin, no admin bar)
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->handle_ajax_requests();
			return;
		}

		// ADMIN or CLI
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			$this->setup_backend_classes();
			return;
		}

		// FRONT-END with admin-bar
		add_action( 'wp_enqueue_scripts', array($this, 'handle_admin_bar_front') );

		// FRONT-END (no ajax, possibly admin bar)
		new EPUB_Posts_Pages_Lookup_Admin_Bar();
	}

	/**
	 * Handle plugin actions here such as saving settings
	 */
	private function handle_action_request() {
		if ( !strncmp($_REQUEST['action'], 'epub_action', strlen('epub_action')) ) {
			// action
		}
	}

	/**
	 * Handle AJAX requests coming from front-end and back-end
	 */
	private function handle_ajax_requests() {
		new EPUB_Settings_Controller();
		new EPUB_Posts_Pages_Lookup_Admin_Bar( true );
	}

	/*
	 * Setup up classes when on ADMIN pages
	 */
	private function setup_backend_classes() {
		global $pagenow;

		// admin core classes
		require_once self::$plugin_dir . 'includes/admin/admin-menu.php';
		add_action( 'admin_enqueue_scripts', 'epub_load_admin_pages_resources' );
		new EPUB_Admin_Notices();
		$this->show_help();

		// admin other classes
		$classes = array(
			// class name=0, loading=1, pages=2, admin_action=3
			[ 'EPUB_Posts_Pages_Lookup_Admin_Bar', ['admin-bar'], [], [] ],
			[ 'EPUB_Publish_Menu_Admin_Bar', ['admin-bar'], ['post.php', 'post-new.php'], [] ]
		);

		$classes = apply_filters( 'epub_backend_classes_to_load', $classes );

		foreach( $classes as $class_info ) {

			// INDIVIDUAL PAGES: if feature available on a specific page then ensure the page is being loaded
			if ( ( ! empty($class_info[2]) && ! in_array($pagenow, $class_info[2]) )  &&
			     ( empty($class_info[3]) || empty($_REQUEST['action']) || ! in_array($_REQUEST['action'], $class_info[3]) ) ) {
				continue;
			}

			// ADMIN BAR:
			if ( in_array('admin-bar', $class_info[1]) ) {
				add_action( 'admin_enqueue_scripts', 'epub_load_admin_bar_resources' );

			// ADMIN NON-BAR:
			} else {
				add_action( 'admin_enqueue_scripts', 'epub_load_admin_features_resources', 101 );
			}

			$new_clas = $class_info[0];
			if ( class_exists($new_clas) ) {
				new $new_clas();
			}
		}
	}

	/**
	 * Invoked on the FRONT-END and checks if admin bar is showing
	 */
	function handle_admin_bar_front() {
		if ( function_exists( 'is_admin_bar_showing' ) && function_exists( 'is_user_logged_in' ) && is_admin_bar_showing() ) {
			epub_load_admin_bar_resources();
		}
	}

	/**
	 * Show help pointers if necessary (during update or first activation of the plugin)
	 * Show Welcome screen if necessary
	 */
	private function show_help() {

		// handle activation of this plugin for the first time
		$plugin_activated_first_time = get_transient( '_epub_plugin_first_time_activated' );
		$plugin_activated_first_time = ! empty($plugin_activated_first_time);
		delete_transient( '_epub_plugin_first_time_activated' );
		if ( $plugin_activated_first_time ) {
			// not this plugin: new EPUB_Help_Pointers( true );
		}

		// handle plugin update
		$plugin_updated = get_transient( '_epub_plugin_updated' );
		$plugin_updated = ! empty($plugin_updated);
		delete_transient( '_epub_plugin_updated' );
		if ( $plugin_updated ) {
			// not this plugin: new EPUB_Help_Pointers( false );
		}

		// show the welcome screen if:
		//  a) plugin was activated for the first time since instal
		//      - link to show location of menus and settings
		//  b) plugin was updated
		//  c) user clicked on Get Started link
		if ( ( isset($_REQUEST['page']) && ( $_REQUEST['page'] == 'epub-welcome-page' ) ) ) {
			new EPUB_Welcome_Screen( false );
		} else if ( $plugin_activated_first_time || $plugin_updated ) {
			new EPUB_Welcome_Screen( true );
		}
	}

	/**
	 * Loads the plugin language files
	 */
	public function load_text_domain() {

		// Set filter for plugin's languages directory
		$lang_dir = dirname( plugin_basename( self::$plugin_file ) ) . '/languages/';
		$lang_dir = apply_filters( 'epub_wp_languages_directory', $lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'epub' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'epub', $locale );

		// Setup paths to current locale file
		$mofile_local  = $lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/epub/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/epub/ folder
			load_textdomain( 'epub', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/<plugin-name>/languages/ folder
			load_textdomain( 'epub', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'epub', false, $lang_dir );
		}
	}

	// Don't allow this singleton to be cloned.
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}

	// Don't allow un-serializing of the class except when testing
	public function __wakeup() {
		if ( strpos($GLOBALS['argv'][0], 'phpunit') === false ) {
			_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
		}
	}

	/**
	 * When developing and debugging we don't need heartbeat
	 */
	function epub_stop_heartbeat() {
		if ( defined( 'RUNTIME_ENVIRONMENT' ) && RUNTIME_ENVIRONMENT == 'DEV' ) {
			wp_deregister_script( 'heartbeat' );
		}
	}
}

/**
 * Returns the single instance of this class
 *
 * @return object - this class instance
 */
function epub_get_instance() {
	return Enhanced_Publishing::instance();
}
epub_get_instance();

