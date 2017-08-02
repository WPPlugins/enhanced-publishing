<?php
/** Various global utility functions */

/**
 * Whether certain functionality is ON or OFF
 *
 * @param $option_name
 * @return bool
 */
function epub_is_option_on( $option_name ) {
	if ( empty(epub_get_instance()->settings_obj) ) {
		return false;
	}
	return epub_get_instance()->settings_obj->get_value( $option_name ) == 'on';
}

/**
 * Get option value
 *
 * @param $option_name
 * @param int $default
 *
 * @return bool
 */
function epub_get_option_value( $option_name, $default=0 ) {
	if ( empty(epub_get_instance()->settings_obj) ) {
		return $default;
	}
	return epub_get_instance()->settings_obj->get_value( $option_name );
}