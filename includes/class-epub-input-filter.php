<?php

/**
 *
 * For input data:
 * 1. Sanitize data
 * 2. Based on field type, also validate data
 * Internal fields have spec with 'internal' => true
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPUB_Input_Filter {

	// basic fields
	const TEXT = 'text';                // use Text or Textarea widget
	const CHECKBOX = 'checkbox';
	const RADIO = 'radio';

	// advanced fields
	const SELECTION = 'select';         // use Dropdown or Radio_buttons_horizontal
	const CHECKBOXES_MULTI_SELECT = 'multi_select';
	const CHECKBOXES_MULTI_SELECT_NOT = 'multi_select_not';

	// custom fields
	const COLOR_HEX = 'color_hex';
	const POSITIVE_NUMBER = 'number';   // use Text widget
	const TRUE_FALSE = 'true_false';

	// special fields
	const ID = 'id';
	const LICENSE_KEY = 'license_key';
	const ENUMERATION = 'enumeration';  // use when input has to be from a list of values


	/**
	 * Validate and sanitize input. If input not in spec then exclude it.
	 *
	 * @param array $input to sanitize - array of settings (key-value pairs)
	 *
	 * @return array|WP_Error returns key - value pairs
	 */
	public function validate_and_sanitize( array $input ) {
		$specification = EPUB_Settings_Specs::get_fields_specification();
		return $this->validate_and_sanitize_specs( $input, $specification );
	}

	public function validate_and_sanitize_specs( array $input, array $specification ) {

		if ( empty($input) ) {
			return new WP_Error('invalid_input', 'Empty input');
		}

		$sanitized_input = array();
		$errors = array();

		// filter each field
		foreach ($input as $key => $input_value) {

			if ( ! isset($specification[$key]) || $input_value === null ) {
				continue;
			}

			$field_spec = $specification[$key];

			$defaults = array(
				'label'       => "Label",
				'type'        => EPUB_Input_Filter::TEXT,
				'optional'    => 'false',
				'max'         => '20',
				'min'         => '3',
				'options'     => array(),
				'internal'    => false,
				'default'     => ''
			);
			$field_spec = wp_parse_args( $field_spec, $defaults );

			// SANITIZE FIELD
			$type = empty($field_spec['type']) ? '' : $field_spec['type'];
			switch ( $type ) {

				case self::CHECKBOXES_MULTI_SELECT:
				case self::CHECKBOXES_MULTI_SELECT_NOT:

					$input_value = is_array($input_value) ? $input_value : array();
					$input_adj = array();
					foreach ( $input_value as $arr_key => $arr_value ) {

						// one choice can have multiple true [key,value] pairs separated by comma
						$arr_value = empty($arr_value) ? '' : $arr_value;
						$tmp = explode(',', $arr_value);
						if ( ! empty($tmp[0]) && ! empty($tmp[1]) ) {
							$arr_key = $tmp[0];
							$arr_value = $tmp[1];
						}
						$input_adj[$arr_key] = sanitize_text_field($arr_value);
					}
					$input_value = $input_adj;
					break;

				default:
					$input_value = trim( sanitize_text_field( $input_value ) );
			}

			// validate/sanitize input
			$result = $this->filter_input_field( $input_value, $field_spec );
			if ( is_wp_error($result) ) {
				// internal fields are assigned defaults if their value is missing or invalid
				if ( empty( $field_spec['internal'] ) ) {
					$errors[] = '<strong>' . $field_spec['label'] . '</strong> is ' . $result->get_error_message();
				} else {
					$sanitized_input[$key] = $field_spec['default'];
				}
			} else {
				$sanitized_input[$key] = $result;
			}

		} // next

		if ( empty($errors) ) {
			return $sanitized_input;
		}

		return new WP_Error('invalid_input', 'validation failed', $errors );
	}

	private function filter_input_field( $value, $field_spec ) {

		// further sanitize the field
		switch ( $field_spec['type'] ) {

			case self::TEXT:
			case self::LICENSE_KEY:
				return $this->filter_text( $value, $field_spec );
				break;

			case self::CHECKBOX:
				return $this->filter_checkbox( $value );
				break;

			case self::SELECTION:
				return $this->filter_select( $value, $field_spec );
				break;

			case self::CHECKBOXES_MULTI_SELECT:
			case self::CHECKBOXES_MULTI_SELECT_NOT:
				// no filtering needed;
				return $value;
				break;

			case self::POSITIVE_NUMBER:
				return $this->filter_positive_number( $value );
				break;

			case self::COLOR_HEX:
				return $this->filter_color_hex( $value, $field_spec );
				break;

			case self::TRUE_FALSE:
				return $this->filter_true_false( $value );
				break;

			case self::ID:
				return $this->filter_id( $value );
				break;

			case self::ENUMERATION:
				return $this->filter_enumeration( $value, $field_spec );
				break;

			default:
				return new WP_Error('epub-invalid-input-type', 'unknown input type: ' . $field_spec['type']);
		}
	}

	/**
	 * Sanitize and validate text. Output WP Error if text too big/small
	 *
	 * @param $text
	 * @param $field_spec
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_text( $text, $field_spec ) {

		if ( is_array($text) ) {
			$text = '';
		}

		if ( strlen($text) > $field_spec['max'] ) {
			$nof_chars_to_remove = strlen($text) - $field_spec['max'];
			return new WP_Error('filter_text_big', 'too large. Remove ' . $nof_chars_to_remove . ' character' . ( $nof_chars_to_remove == 1 ? '.' : 's.') );
		}

		if ( ( empty($text) && ! isset($field_spec['optional']) ) || ( strlen($text) > 0 && strlen($text) < $field_spec['min'] ) ) {
			$nof_chars_to_remove = $field_spec['min'] - strlen($text);
			return new WP_Error('filter_text_small', 'too short. Add at least ' . $nof_chars_to_remove . ' character' . ( $nof_chars_to_remove == 1 ? '.' : 's.') );
		}

		return $text;
	}

	/**
	 * Sanitize and validate selection. Output WP Error if text is not in the selection
	 *
	 * @param $value
	 * @param $field_spec
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_select( $value, $field_spec ) {

		if ( ! in_array( $value, array_keys($field_spec['options']) )  && ! isset($field_spec['optional']) ) {
			return new WP_Error('filter_selection_invalid', 'not valid. Valid values are: ' . EPUB_Utilities::get_variable_string($field_spec['options']));
		}

		return $value;
	}

	/**
	 * Sanitize and validate checkbox.
	 *
	 * @param $value
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_checkbox( $value ) {

		if ( empty($value) || $value == 'off' ) {
			return "off";
		} else if ( $value == "on" ) {
			return $value;
		}

		return new WP_Error('filter_checkbox_invalid', 'value is not valid');
	}

	/**
	 * Sanitize and validate positive number
	 *
	 * @param $number
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_positive_number( $number ) {

		if ( ! EPUB_Utilities::is_positive_int( $number ) ) {
			return new WP_Error('filter_not_positive_number', 'not a positive number.');
		}

		return $number;
	}

	/**
	 * Sanitize and validate true/false value
	 *
	 * @param $boolean
	 *
	 * @return string|WP_Error returns sanitized and validated text
	 */
	private function filter_true_false( $boolean ) {
		return $boolean === true;
	}

	/**
	 * Sanitize and validate HEX color number
	 *
	 * @param $value
	 * @param $field_spec
	 *
	 * @return string|WP_Error
	 */
	public function filter_color_hex( $value, $field_spec=array() ) {

		// Check for a hex color string '#c1c2b4'
		if ( preg_match('/^#[a-f0-9]{6}$/i', $value) )
		{
			return $value;
		}

		// Check for a hex color string without hash 'c1c2b4'
		else if(preg_match('/^[a-f0-9]{6}$/i', $value))
		{
			return '#' . $value;
		}

		if ( empty($value) && isset($field_spec['optional']) ) {
			return $value;
		}

		return new WP_Error('filter_not_color_hex', 'enter color in HEX e.g. #FAFAD2');
	}

	/**
	 * Sanitize and validate ID
	 *
	 * @param $id
	 *
	 * @return int|WP_Error
	 */
	private function filter_id( $id ) {
		$id = EPUB_Utilities::sanitize_get_id( $id );
		if ( is_wp_error($id) ) {
			return new WP_Error('filter_not_id', ' with internal error (' . $id->get_error_code() . ')');
		}
		return $id;
	}

	/**
	 * Input has to match one of the predefined values.
	 *
	 * @param $value
	 * @param $field_spec
	 *
	 * @return mixed - WP_Error | valid value
	 */
	private function filter_enumeration( $value, $field_spec ) {
		if ( in_array( $value, $field_spec['options'] ) ) {
			return $value;
		}

		return new WP_Error('filter_not_enumeration', 'enter value not in enumeration');
	}
}
