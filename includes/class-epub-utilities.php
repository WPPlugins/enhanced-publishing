<?php

/**
 * Various utility functions
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPUB_Utilities {

	public static function adjust_post_title( $post_title, $max_len=45 ) {
		$title = empty( $post_title ) ? '[No Title]' : $post_title;
		$title = strlen( $title ) > $max_len ? substr( $title, 0, $max_len) . ' [...]' : $title;
		return $title;
	}

	public static function get_post_status_text( $post_status ) {

		$post_statuses = array( 'draft' => 'Draft', 'pending' => 'Pending',
		                       'publish' => 'Published', 'future' => 'Scheduled', 'private' => 'Private' );

		if ( empty($post_status) || ! in_array($post_status, array_keys($post_statuses)) ) {
			return $post_status;
		}

		return $post_statuses[$post_status];

	}

	/**************************************************************************************************************************
	 *
	 *                     STRING OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * PHP substr() function returns FALSE if the input string is empty. This method
	 * returns empty string if input is empty or error occurs.
	 *
	 * @param $string
	 * @param $start
	 * @param null $length
	 *
	 * @return string
	 */
	public static function substr( $string, $start, $length=null ) {
		$result = substr($string, $start, $length);
		return empty($result) ? '' : $result;
	}

	/**************************************************************************************************************************
	 *
	 *                     NUMBER OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * Determine if value is positive integer
	 * @param int $number is check
	 * @return bool
	 *
	 * TESTED with PHPUnit
	 */
	public static function is_positive_int( $number ) {

		if ( empty($number) || ! is_numeric($number) ) {
			return false;
		}

		if ( ( (int) $number) != ( (float) $number )) {
			return false;
		}

		$number = (int) $number;

		return is_int($number) && $number > 0;
	}

	/**
	 * Determine if value is positive integer
	 * @param int $number is check
	 * @return bool
	 *
	 * TESTED with PHPUnit
	 */
	public static function is_positive_or_zero_int( $number ) {

		if ( ! isset($number) || ! is_numeric($number) ) {
			return false;
		}

		if ( ( (int) $number) != ( (float) $number )) {
			return false;
		}

		$number = (int) $number;

		return is_int($number);
	}

	/**
	 * Remove non-numeric characters from number
	 * @param $number
	 * @return mixed
	 */
	public static function filter_number( $number ) {
		return empty($number) ? 0 : preg_replace("/[^0-9,.]/", "", $number );
	}


	/**************************************************************************************************************************
	 *
	 *                     DATE OPERATIONS
	 *
	 **************************************************************************************************************************/

	/**
	 * Retrieve specific format from given date-time string e.g. '10-16-2003 10:20:01' becomes '10-16-2003'
	 *
	 * @param $datetime_str
	 * @param string $format e.g. 'Y-m-d H:i:s'  or  'M j, Y'
	 *
	 * @return string formatted date or the original string
	 */
	public static function get_formatted_datetime_string( $datetime_str, $format='M j, Y' ) {

		if ( empty($datetime_str) || empty($format) ) {
			return $datetime_str;
		}

		$time = strtotime($datetime_str);
		if ( empty($time) ) {
			return $datetime_str;
		}

		$date_time = date($format, $time);
		if ( $date_time == $format ) {
			$date_time = $datetime_str;
		}

		return empty($date_time) ? $datetime_str : $date_time;
	}

	/**
	 * Get nof hours passed between two dates.
	 *
	 * @param string $date1
	 * @param string $date2 OR if empty then use current date
	 *
	 * @return int - number of hours between dates [0-x] or null if error
	 */
	public static function get_hours_since( $date1, $date2='' ) {

		try {
			$date1_dt = new DateTime( $date1 );
			$date2_dt = new DateTime( $date2 );
		} catch(Exception $ex) {
			return null;
		}

		if ( empty($date1_dt) || empty($date2_dt) ) {
			return null;
		}

		$hours = date_diff($date1_dt, $date2_dt)->h;

		return $hours === false ? null : $hours;
	}

	/**
	 * Get nof days passed between two dates.
	 *
	 * @param string $date1
	 * @param string $date2 OR if empty then use current date
	 *
	 * @return int - number of days between dates [0-x] or null if error
	 */
	public static function get_days_since( $date1, $date2='' ) {

		try {
			$date1_dt = new DateTime( $date1 );
			$date2_dt = new DateTime( $date2 );
		} catch(Exception $ex) {
			return null;
		}

		if ( empty($date1_dt) || empty($date2_dt) ) {
			return null;
		}

		$days = date_diff($date1_dt, $date2_dt)->days;

		return $days === false ? null : $days;
	}


	/**
	 * How long ago pass date occurred.
	 *
	 * @param string $date1
	 *
	 * @return string x year|month|week|day|hour|minute|second(s) or '[unknown]' on error
	 */
	public static function time_since_today( $date1 ) {
		return self::how_long_ago( $date1 );
	}

	/**
	 * How long ago since now.
	 *
	 * @param string $date1
	 * @param string $date2 or if empty use current time
	 *
	 * @return string x year|month|week|day|hour|minute|second(s) or '[unknown]' on error
	 */
	public static function how_long_ago( $date1, $date2='' ) {

		$time1 = strtotime($date1);
		$time2 = empty($date2) ? time() : strtotime($date2);
		if ( empty($time1) || empty($time2) ) {
			return '[unknown]';
		}

		$time = abs($time2 - $time1);
		$time = ( $time < 1 )? 1 : $time;
		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'min',
			1 => 'sec'
		);

		$output = '';
		foreach ($tokens as $unit => $text) {
			if ($time >= $unit) {
				$numberOfUnits = floor($time / $unit);
				$output =  $numberOfUnits . ' ' . $text . ( $numberOfUnits >1 ? 's' : '');
				break;
			}
		}

		return $output;
	}


	/**************************************************************************************************************************
	 *
	 *                     NOTICES
	 *
	 *************************************************************************************************************************/

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param string $message
	 * @param string $title
	 * @param string $type
	 */
	public static function ajax_show_info_die( $message, $title='', $type='success' ) {
		wp_die( json_encode( array( 'message' => self::get_html_message_box( $message, $title, $type) ) ) );
	}

	/**
	 * AJAX: Used on response back to JS. will call wp_die()
	 *
	 * @param $message
	 * @param string $title
	 */
	public static function ajax_show_error_die( $message, $title='' ) {
		wp_die( json_encode( array( 'error' => true, 'message' => self::get_html_message_box( $message, $title, 'error') ) ) );
	}

	/**
	 * Show info or error message to the user
	 *
	 * @param $message
	 * @param string $title
	 * @param string $type
	 *
	 * @return string
	 */
	public static function get_html_message_box( $message, $title='', $type='success' ) {
		$title = empty($title) ? '' : '<h4>' . $title . '</h4>';
		$message = empty($message) ? '' : $message;
		return
			"<div class='notification'>
				<span class='$type'>
					$title
					<p>$message</p>
				</span>
			</div>";
	}

	/**
	 * Show admin notice at the top of page
	 *
	 * @param $msg_code
	 * @param string $redirect
	 * @param string $param
	 */
	public static function show_top_level_admin_msg_and_redirect( $msg_code, $redirect='admin.php', $param='' ) {
		$url = empty( $_REQUEST['epub_redirect'] ) ? admin_url( $redirect ) : $_REQUEST['epub_redirect'];

		$query = array();
		$query['epub_admin_notice'] = urlencode($msg_code);
		if ( ! empty($param) ) {
			$query['epub_notice_param'] = $param;
		}

		$redirect = add_query_arg( $query, $url );
		wp_safe_redirect( $redirect );
		defined('EPUB_TESTING') ? wp_die($msg_code) : die();
	}

	/**
	 * Redirect
	 *
	 * @param string $redirect
	 */
	public static function admin_redirect( $redirect='admin.php' ) {
		$url = empty( $_REQUEST['epub_redirect'] ) ? admin_url( $redirect ) : $_REQUEST['epub_redirect'];
		$query = array();
		$redirect = add_query_arg( $query, $url );
		wp_safe_redirect( $redirect );
		defined('EPUB_TESTING') ? wp_die() : die();
	}

	public static function user_not_logged_in() {
		self::ajax_show_error_die( '<p>You are not logged in. Refresh your page and log in.</p>', 'Cannot save your changes' );
	}

	
	/**************************************************************************************************************************
	 *
	 *                     OTHER
	 *
	 *************************************************************************************************************************/

	/**
	 * Return string representation of given variable for logging purposes
	 *
	 * @param $var
	 *
	 * @return string
	 */
	public static function get_variable_string( $var ) {

		if ( $var === null ) {
			return '<null>';
		}

		if ( ! isset($var) ) {
			return '<not set>';
		}

		if ( is_array( $var ) && empty($var)) {
			return '[]';
		}

		if ( is_array( $var ) ) {
			$output = 'array';
			$ix = 0;
			foreach ($var as $key => $value) {

				if ( is_object( $value ) ) {
					$value = '<' . get_class( $value ) . '>';
				} elseif ( is_array($value) ) {
					$value = '[...]';
				} else {
					$value = $value . ( strlen($value) > 50 ? '...' : '');
					$value = is_numeric($value) ? $value : "'" . $value . "'";
				}

				$output .= "['" . $key . "' => " . $value . "]";

				if ( $ix++ > 10 ) {
					$output .= '[...]';
					break;
				}
			}
			return $output;
		}

		if ( is_object( $var ) ) {
			return '<' . get_class($var) . '>';
		}

		return $var;
	}

	/**
	 * Retrieve roles that current user has
	 * @return array $roles
	 *
	 * Not tested - simple
	 */
	public static function get_user_roles() {
		global $wp_roles;

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();

		return $roles;
	}

	/**
	 * Retrieve ID or return error.
	 *
	 * @param mixed $id is either $id number or array with 'id' index
	 *
	 * @return int|WP_Error
	 */
	public static function sanitize_get_id( $id ) {

		if ( ! isset( $id) ) {
			EPUB_Logging::add_log( 'Error occurred: E001' );
			return new WP_Error('E001', "invalid ID");
		}

		if ( is_array( $id) ) {
			if ( !isset( $id['id']) ) {
				EPUB_Logging::add_log( 'Error occurred: E002' );
				return new WP_Error('E002', "invalid ID");
			}

			$id_value = $id['id'];
			if ( ! self::is_positive_int( $id_value ) ) {
				EPUB_Logging::add_log( 'Error occurred: E003' );
				return new WP_Error('E003', "invalid ID: " . self::get_variable_string($id_value));
			}

			return (int) $id_value;
		}

		if ( ! self::is_positive_int( $id ) ) {
			EPUB_Logging::add_log( 'Error occurred: E004' );
			return new WP_Error('E004', "invalid ID: " . $id);
		}

		return (int) $id;
	}

	/**
	 * Place form fields into an array. If field doesn't exist don't consider it.
	 *
	 * @param $new_query_string
	 * @param $all_fields_specs
	 *
	 * @return array of name - value pairs
	 */
	public static function retrieve_form_fields( $new_query_string, $all_fields_specs ) {

		$name_values = array();
		$new_query_string = isset($new_query_string) ? $new_query_string : '';
		parse_str($new_query_string, $submitted_fields);

		foreach ($all_fields_specs as $key => $spec ) {

			// ignore fields that are internal
			if ( !empty($spec['internal']) || $spec['type'] == EPUB_Input_Filter::ID ) {

					continue;
			}

			// checkboxes in a box have zero or more values
			$is_multiselect =  $spec['type'] == EPUB_Input_Filter::CHECKBOXES_MULTI_SELECT;
			if ( $is_multiselect ||  $spec['type'] == EPUB_Input_Filter::CHECKBOXES_MULTI_SELECT_NOT) {

				$multi_selects = array();
				foreach ($submitted_fields as $submitted_key => $submitted_value) {
					if ( ! empty($submitted_key) && strpos($submitted_key, 'epub_' . $key) === 0) {

						$chunks = $is_multiselect ?  explode('[[-,-]]', $submitted_value) : explode('[[-HIDDEN-]]', $submitted_value);
						if ( empty($chunks[0]) || empty($chunks[1]) || ! empty($chunks[2]) ) {
							continue;
						}

						if ( $is_multiselect ) {
							$multi_selects[$chunks[0]] = $chunks[1];
						} else if ( ! empty($submitted_value) && strpos($submitted_value, '[[-HIDDEN-]]') !== false ) {

							$multi_selects[$chunks[0]] = $chunks[1];
						}
					}
				}

				$name_values += array( $key => $multi_selects );
				continue;
			}

			// input value missing
			if ( empty($submitted_fields[ 'epub_' . $key ]) ) {
				// empty checkbox and radio button is the same as 'off
				if ( $spec['type'] == EPUB_Input_Filter::CHECKBOX || $spec['type'] == EPUB_Input_Filter::RADIO ) {
					$input_value = 'off';
				// supply defaults for other empty values
				} else if ( empty($field_spec['optional'])  ) {
					$input_value = isset( $spec['default'] ) ? $spec['default'] : '';
				}
			// otherwise get the input field value
			} else {
				$input_value = trim( $submitted_fields[ 'epub_' . $key ] );
			}

			$name_values += array( $key => $input_value);
		}

		return $name_values;
	}

	/**
	 * Array1 VALUES NOT IN array2
	 *
	 * @param $array1
	 * @param $array2
	 *
	 * @return array of values in array1 NOT in array2
	 */
	public static function diff_two_dimentional_arrays( $array1, $array2 ) {

		if ( empty($array1) ) {
			return array();
		}

		if ( empty($array2) ) {
			return $array1;
		}

		// flatten first array
		foreach( $array1 as $key => $value ) {
			if ( is_array($value) ) {
				$tmp_value = '';
				foreach( $value as $tmp ) {
					$tmp_value .= ( empty($tmp_value) ? '' : ',' ) . ( empty($tmp) ? '' : $tmp );
				}
				$array1[$key] = $tmp_value;
			}
		}

		// flatten second array
		foreach( $array2 as $key => $value ) {
			if ( is_array($value) ) {
				$tmp_value = '';
				foreach( $value as $tmp ) {
					$tmp_value .= ( empty($tmp_value) ? '' : ',' ) . ( empty($tmp) ? '' : $tmp );
				}
				$array2[$key] = $tmp_value;
			}
		}

		return array_diff_assoc($array1, $array2);
	}
}
