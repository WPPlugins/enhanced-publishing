<?php

/**
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPUB_Admin_Notices {

	public function __construct() {
		add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
		//add_action( 'epub_dismiss_notices', array( $this, 'dismiss_admin_notices' ) );
	}

	/**
	 * Show noticers for admin at the top of the page
	 */
	public function show_admin_notices() {

		if ( ! empty($_GET['epub_admin_notice']) ) {

			$param = '';
			if ( ! empty($_GET['epub_notice_param']) ) {
				$param = ' ' . sanitize_text_field( $_GET['epub_notice_param'] );
			}

			$class = 'error';
			switch ( $_GET['epub_admin_notice'] ) {

				case 'ep_refresh_page' :
					$message = __( 'Refresh your page', 'epub' );
					break;
				case 'ep_refresh_page_error' :
					$message = __( 'Error occurred. Please refresh your browser and try again', 'epub' );
					break;
				case 'ep_security_failed' :
					$message = __( 'You do not have permission', 'epub' );
					break;
				default:
					$message = 'unknown error (133)';
					break;
			}

			echo '<div class="' . esc_attr( 'epub' . $class ) . '"><p><strong>' .  $message  . '</strong></p></div>';
		}
	}

}
