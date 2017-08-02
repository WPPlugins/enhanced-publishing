<?php  if ( ! defined( 'ABSPATH' ) ) exit;

spl_autoload_register(array('EPUB_Autoloader', 'autoload'), false);

/**
 * A class which contains the autoload function, that the spl_autoload_register
 * will use to autoload PHP classes.
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 */
class EPUB_Autoloader {

	public static function autoload( $class ) {
		static $classes = null;

		if ( $classes === null ) {
			$classes = array(

				// CORE
				'epub_utilities'                    =>  'includes/class-epub-utilities.php',
				'epub_html_elements'                =>  'includes/class-epub-html-elements.php',
				'epub_input_filter'                 =>  'includes/class-epub-input-filter.php',

				// SYSTEM
				'epub_logging'                      =>  'includes/system/class-epub-logging.php',
				'epub_help_pointers'                =>  'includes/system/class-epub-help-pointers.php',
				'epub_help_upgrades'                =>  'includes/system/class-epub-help-upgrades.php',

				// ADMIN CORE
				'epub_admin_notices'                =>  'includes/admin/class-epub-admin-notices.php',
				'epub_welcome_screen'               =>  'includes/admin/class-epub-welcome-screen.php',

				// ADMIN PLUGIN MENU PAGES
				'epub_settings_controller'          =>  'includes/admin/settings/class-epub-settings-controller.php',
				'epub_settings_specs'               =>  'includes/admin/settings/class-epub-settings-specs.php',
				'epub_settings_db'                  =>  'includes/admin/settings/class-epub-settings-db.php',

				// FEATURES
				'epub_posts_pages_lookup_admin_bar' =>  'includes/features/posts-pages-lookup/class-epub-posts-pages-lookup-admin-bar.php',
				'epub_posts_pages_lookup_settings'  =>  'includes/features/posts-pages-lookup/class-epub-posts-pages-lookup-settings.php',

				'epub_publish_menu_admin_bar'       =>  'includes/features/publishing-menu/class-epub-publish-menu-admin-bar.php',
				'epub_publish_menu_settings'        =>  'includes/features/publishing-menu/class-epub-publish-menu-settings.php',
			);
		}

		$cn = strtolower( $class );
		if ( isset( $classes[ $cn ] ) ) {
			/** @noinspection PhpIncludeInspection */
			include_once( Enhanced_Publishing::$plugin_dir . $classes[ $cn ] );
		}
	}
}
