<?php

/**
 * Handle saving feature settings.
 */
class EPUB_Settings_Controller {

	public function __construct() {
		add_action( 'wp_ajax_epub_save_settings', array( $this, 'save_settings' ) );
		add_action( 'wp_ajax_nopriv_epub_save_settings', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epub_send_feedback', array( $this, 'send_feedback' ) );
		add_action( 'wp_ajax_nopriv_epub_send_feedback', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epub_close_welcome_header', array( $this, 'close_welcome_header' ) );
	}

	/**
	 * Triggered when user submits Save to plugin settings. Saves the updated settings into the database
	 */
	public function save_settings() {

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epub_save_settings'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epub_save_settings'], '_wpnonce_epub_save_settings' ) ) {
			EPUB_Utilities::ajax_show_error_die('Refresh your page (1)');
		}

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			EPUB_Utilities::ajax_show_error_die('You do not have permission to edit settings');
		}

		// retrieve current plugin settings
		$orig_settings = epub_get_instance()->settings_obj->get_settings();
		
		// retrieve user input
		$field_specs = EPUB_Settings_Specs::get_fields_specification();
		$new_settings = EPUB_Utilities::retrieve_form_fields( $_POST['form'], $field_specs );
		
		// sanitize and save settings in the database. see EPUB_Settings_DB class
		$result = epub_get_instance()->settings_obj->update_settings( $new_settings );
		if ( is_wp_error( $result ) ) {
			/* @var $result WP_Error */
			$message = $result->get_error_data();
			if ( empty($message) ) {
				EPUB_Utilities::ajax_show_error_die( $result->get_error_message(), 'Could not save settings:' );
			} else {
				EPUB_Utilities::ajax_show_error_die( $this->generate_error_summary( $message ), 'Settings NOT saved due to following problems:' );
			}
		}

		// some settings require page reload
		$reload = $this->is_page_reload( $orig_settings, $new_settings, $field_specs);

		// we are done here
		EPUB_Utilities::ajax_show_info_die( $reload ? 'reload Settings saved. PAGE WILL RELOAD NOW.' : 'Settings saved' );
	}

	private function is_page_reload( $orig_settings, $new_settings, $spec ) {

		$diff = EPUB_Utilities::diff_two_dimentional_arrays( $new_settings, $orig_settings );
		foreach( $diff as $key => $value ) {
			if ( ! empty($spec[$key]['reload']) ) {
				return true;
			}
		}

		return false;
	}

	private function generate_error_summary( $errors ) {

		$output = '';

		if ( empty( $errors ) || ! is_array( $errors )) {
			return $output . 'unknown error (344)';
		}

		$output .= '<ol>';
		foreach( $errors as $error ) {
			$output .= '<li>' . wp_kses( $error, 'strong' ) . '</li>';
		}
		$output .= '</ol>';

		return $output;
	}

	/**
	 * Triggered when user submits feedback. Send email to the Echo Plugin team.
	 */
	public function send_feedback() {

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epub_send_feedback'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epub_send_feedback'], '_wpnonce_epub_send_feedback' ) ) {
			EPUB_Utilities::ajax_show_error_die('Security check failed (3)');
		}

		// ensure user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			EPUB_Utilities::ajax_show_error_die('Please try again later (4)');
		}

		// retrieve user input
		$user_email = sanitize_email( $_POST['email'] );
		$user_email = empty($user_email) ? '[email missing]' : substr( $user_email, 0, 50 );
		$user_feedback = sanitize_text_field( $_POST['feedback'] );
		$user_feedback = empty($user_feedback) ? '[user feedback missing]' : substr( $user_feedback, 0, 1000 );

		// send feedback
		$api_params = array(
			'epub_action'   	=> 'epub_process_user_feedback',
			'user_email' 	    => $user_email,
			'user_feedback'	    => $user_feedback, // the name of our product in EDD
			'plugin_name'       => 'Enhanced Publishing'
		);

		// Call the API
		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoplugins.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,
				'sslverify' => false
			)
		);
		if ( is_wp_error( $response ) ) {
			EPUB_Utilities::ajax_show_error_die( 'Please contact us at: https://www.echoplugins.com/contact-us/', 'An error occurred' );
		}

		// we are done here
		EPUB_Utilities::ajax_show_info_die( 'Feedback sent. Thank you!' );
	}

	/**
	 * Record that user closed the welcome header or update message on the settings page
	 */
	public function close_welcome_header() {
		delete_option('epub_show_welcome_header');
	}

	public function user_not_logged_in() {
		EPUB_Utilities::ajax_show_error_die( '<p>You are not logged in. Refresh your page and log in.</p>', 'Cannot save your changes' );
	}
}
