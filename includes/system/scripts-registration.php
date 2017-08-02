<?php

/**  Register JS and CSS files  */

/**
 * FRONT-END pages using our plugin features
 */
function epub_load_public_resources() {
	// if SCRIPT_DEBUG is off then use minified scripts
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style('epub-public-styles', Enhanced_Publishing::$plugin_url . 'css/public_styles' . $suffix . '.css', array(), Enhanced_Publishing::$version );
	wp_enqueue_script('epub-public-scripts', Enhanced_Publishing::$plugin_url . 'js/public_scripts' . $suffix . '.js',
			array('jquery', 'jquery-ui-core','jquery-effects-core','jquery-effects-bounce'), Enhanced_Publishing::$version );
}
// NOT USED add_action( 'wp_enqueue_scripts', 'epub_load_public_resources' );

/**
 * ADMIN-PLUGIN MENU PAGES (Plugin settings, reports, lists etc.)
 */
function epub_load_admin_pages_resources(  ) { 

	// if SCRIPT_DEBUG is off then use minified scripts
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style('epub-admin-plugin-pages-styles', Enhanced_Publishing::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Enhanced_Publishing::$version );
	wp_enqueue_script('epub-admin-plugin-pages-scripts', Enhanced_Publishing::$plugin_url . 'js/admin-plugin-pages' . $suffix . '.js',
									array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core','jquery-effects-bounce', 'wp-color-picker'), Enhanced_Publishing::$version );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );
}

/**
 * ADMIN-FEATURES complementing WP ADMIN PAGES (not ADMIN BAR)
 */
function epub_load_admin_features_resources() {

	// if SCRIPT_DEBUG is off then use minified scripts
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	// EMPTY CSS wp_enqueue_style('epub-admin-features-styles', Enhanced_Publishing::$plugin_url . 'css/admin-features' . $suffix . '.css', array(), Enhanced_Publishing::$version );
	wp_enqueue_script('epub-admin-features-scripts', Enhanced_Publishing::$plugin_url . 'js/admin-features' . $suffix . '.js',
									array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core'), Enhanced_Publishing::$version );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );
}

/**
 * ADMIN-BAR is visible  (BACK-END and sometimes user sign-in on FRONT-END)
 */
function epub_load_admin_bar_resources() {

	// if SCRIPT_DEBUG is off then use minified scripts
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style('epub-admin-bar-styles', Enhanced_Publishing::$plugin_url . 'css/admin-bar' . $suffix . '.css', array(), Enhanced_Publishing::$version );
	wp_enqueue_script('epub-admin-bar-scripts', Enhanced_Publishing::$plugin_url . 'js/admin-bar' . $suffix . '.js', array('jquery'), Enhanced_Publishing::$version );
	wp_localize_script( 'epub-admin-bar-scripts', 'epub_scripts', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
