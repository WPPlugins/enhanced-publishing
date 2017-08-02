<?php

/**
 * Setup links and information on Plugins WordPress page
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 */


/**
 * Adds various links for plugin on the Plugins page displayed on the left
 *
 * @param   array $links contains current links for this plugin
 * @return  array returns an array of links
 */
function epub_add_plugin_action_links ( $links ) {
	$my_links = array(
		'Settings'  => '<a href="' . admin_url('options-general.php?page=epub-settings') . '">Settings</a>',
		'Docs'      => '<a href="http://www.echoplugins.com/documentation/enhanced-publishing/" target="_blank">Docs</a>',
		'Support'   => '<a href="https://www.echoplugins.com/contact-us/?inquiry-type=technical&plugin_type=enhanced-publishing">Support</a>'
	);

	return array_merge( $my_links, $links );
}
add_filter( 'plugin_action_links_' . plugin_basename(Enhanced_Publishing::$plugin_file), 'epub_add_plugin_action_links', 10, 2 );

/**
 * Add info about plugin on the Plugins page displayed on the right.
 *
 * @param $links
 * @param $file
 * @return array
 */
function epub_add_plugin_row_meta($links, $file) {
	if ( $file != 'enhanced-publishing/enhanced-publishing.php' ) {
		return $links;
	}

	$links[] = '<a href="' . admin_url( 'index.php?page=epub-welcome-page&tab=get-started' ) . '">Getting Started</a>';
	return $links;
}
add_filter( 'plugin_row_meta', 'epub_add_plugin_row_meta', 10, 2 );
