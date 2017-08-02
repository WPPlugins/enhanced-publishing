<?php

/**
 * Setup WordPress menu for this plugin
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 */

require_once Enhanced_Publishing::$plugin_dir . 'includes/admin/settings/settings-page.php';

/**
 *  Register plugin menus
 */
function epub_add_plugin_menus() {

	$feature_settings_page = add_options_page( 'Enhanced Publishing', 'Enhanced Publishing', 'manage_options', 'epub-settings', 'epub_display_features_settings_page' );
	if ( $feature_settings_page === false ) {
		return;
	}

	// Register (i.e. whitelist) the option for the configuration pages.
	register_setting('epub_settings', 'epub_settings');

	// load scripts needed for Features Settings page only on that page
	add_action( 'load-' . $feature_settings_page, 'epub_load_admin_pages_resources' );
}
add_action( 'admin_menu', 'epub_add_plugin_menus' );
