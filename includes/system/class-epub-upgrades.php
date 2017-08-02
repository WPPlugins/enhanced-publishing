<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if plugin upgrade to a new version requires any actions like database upgrade
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 */
class EPUB_Upgrades {

	private $plugin_upgraded = false;

	function test() {
		$version = get_option( 'epub_version' );

		// any plugin update will show the welcome screen
		if ( version_compare( $version, '2.0.0', '<' ) ) {
			//$this->v11_upgrades();
		}

		// If upgrades have occurred
		/* if ( $this->$plugin_upgraded ) {
			update_option( 'epub_version', Enhanced_Publishing::$version );
		} */

	}

}
