<?php

/**
 * Activate the plugin
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
*/

/**
 * Activate this plugin i.e. setup tables, data etc.
 */
function epub_activate_plugin() {


	/**************    SETUP  WELCOME / GET STARTED PAGES   ****************/

	$plugin_version = get_option( 'epub_version' );
	$plugin_first_activated = empty($plugin_version);
	$plugin_updated = $plugin_first_activated && ( $plugin_version != Enhanced_Publishing::$version );

	if ( $plugin_first_activated ) {
		update_option('epub_show_welcome_header', true);
		set_transient( '_epub_plugin_first_time_activated', true, 30 );
		set_transient( '_epub_plugin_just_installed', true, 30 );
	}

	if ( $plugin_updated ) {
		set_transient( '_epub_plugin_updated', true, 30 );
	}

	// update plugin version; will be used for plugin upgrades
	update_option( 'epub_version', Enhanced_Publishing::$version );
}
register_activation_hook( Enhanced_Publishing::$plugin_file, 'epub_activate_plugin' );

/**
 * User deactivates this plugin so refresh the permalinks
 */
function epub_deactivation() {

	// Clear the permalinks to remove our post type's rules
	flush_rewrite_rules();

}
// NOT APPLICABLE FOR THIS PLUGIN
// register_deactivation_hook( __FILE__, 'epub_deactivation' );
