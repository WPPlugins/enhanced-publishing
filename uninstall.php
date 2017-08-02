<?php

// Exit if accessed directly
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;


/**
 * Uninstall this plugin
 *
 */


/** Delete plugin options */
delete_option( 'epub_settings' );
delete_option( 'epub_show_welcome_header' );
delete_option( 'epub_version' );
delete_option( 'epub_error_log' );

