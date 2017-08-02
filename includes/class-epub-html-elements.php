<?php

/**
 * All methods are static with no side effects i.e. don't change some global states and don't exhibit other non-testable behaviors
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPUB_HTML_Elements {

	/**
	 * Renders an HTML Text field
	 *
	 * @param array $args Arguments for the text field
	 * @return string Text field
	 */
	public function text( $args = array() ) {

		$defaults = array(
				'id'           => '',
				'name'         => 'text',
				'value'        => '',
				'label'        => '',
				'desc'         => '',
				'info'         => '',
				'placeholder'  => '',
				'class'        => 'regular-text',
				'readonly'     => false,  // will not be submitted
				'autocomplete' => false,
				'data'         => false,
				'size'          => 3,
				'max'          => 50
		);
		$args = wp_parse_args( $args, $defaults );

		$id =  esc_attr( $args['name'] );
		$autocomplete = ( $args['autocomplete'] ? 'on' : 'off' );
		$readonly = $args['readonly'] ? ' readonly' : '';

		$data = '';
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= "data-$key=$value";
			}
		}

		$output = "<span id='{$id}-wrap'>";

		$output .= "<label class='label' for='{$id}'>" . esc_html( $args['label'] ) . "</label>";

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$output .= "<input type='text' name='{$id}' id='{$id}' class='{$args['class']}' autocomplete='$autocomplete' value='" . esc_attr( $args['value'] ) .
		           "' placeholder='" . esc_attr( $args['placeholder'] ) . "'" . $data . $readonly . " maxlength='{$args['max']}' />";

		if ( ! empty( $args['info'] ) ) {
			$output .= "<span class='info-icon'><p class='hidden'>{$args['info']}</p></span>";
		}

		$output .= '</span>';

		return $output;
	}

	/**
	 * Renders an HTML textarea
	 *
	 * @param array $args Arguments for the textarea
	 * @return string textarea
	 */
	public function textarea( $args = array() ) {
		$defaults = array(
			'name'        => 'textarea',
			'value'       => '',
			'label'       => null,
			'desc'        => null,
			'class'       => 'large-text',
			'disabled'    => false,
			'rows'        => 4
		);
		$args = wp_parse_args( $args, $defaults );

		$disabled = '';
		if( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}
		$id = empty($args['name']) ? '' :  'id="' . esc_attr( $args['name'] ) . '"';

		$output = '<span id="' . sanitize_key( $args['name'] ) . '-wrap">';

		$output .= '<label class="label" for="' . sanitize_key( $args['name'] ) . '">' . esc_html( $args['label'] ) . '</label>';

		$output .= '<textarea rows="' . esc_attr( $args['rows'] ) . '" name="' . esc_attr( $args['name'] ) . '"' .$id . '" class="' . $args['class'] . '"' . $disabled . '>' . esc_attr( $args['value'] ) . '</textarea>';

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$output .= '</span>';

		if ( ! empty( $args['info'] ) ) {
			$output .= '<span class="info-icon"><p class="hidden">' . $args['info'] . '</p></span>';
		}

		return $output;
	}

	/**
	 * Renders an HTML Checkbox
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function checkbox( $args = array() ) {

		$defaults = array(
			'id'           => '',
			'name'         => 'checkbox',
			'value'        => '',
			'label'        => '',
			'desc'         => '',
			'info'         => '',
			'class'        => 'epub-checkbox',
			'options'      => array()
		);
		$args = wp_parse_args( $args, $defaults );

		$output = '<label class="label" for="epub_' . $args['name'] . '">' . esc_html( $args['label'] ) . '</label>';

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$id = empty($args['name']) ? '' :  'id="' . esc_attr('epub_' . $args['name'] ) . '"';

		$checked = checked( "on", $args['value'], false );

		$output .= '<input type="checkbox" name="epub_' . $args['name']  . '"' . $id . ' class="' . $args['class'] . '"' . $checked . ' />';

		if ( ! empty( $args['info'] ) ) {
			$output .= '<span class="info-icon"><p class="hidden">' . $args['info'] . '</p></span>';
		}

		return $output;

	}

	/**
	 * Renders an HTML radio button
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function radio_button( $args = array() ) {


		$defaults = array(
			'id'           => '',
			'name'         => 'radio-buttons',
			'value'        => '',
			'label'        => '',
			'desc'         => '',
			'info'         => '',
			'class'        => 'epub-radio-button',
			'options'      => array()
		);
		$args = wp_parse_args( $args, $defaults );

		$output = '<label class="label" for="epub_' . $args['name'] . '">' . esc_html( $args['label'] ) . '</label>';

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$id = empty($args['name']) ? '' :  'id="' . esc_attr('epub_' . $args['name'] ) . '"';

		$checked = checked( 1, $args['value'], false );

		$output .= '<input type="radio" name="epub_' . $args['name']  . '"' . $id . ' class="' . $args['class'] . '"' . $checked . ' />';

		if ( ! empty( $args['info'] ) ) {
			$output .= '<span class="info-icon"><p class="hidden">' . $args['info'] . '</p></span>';
		}

		return $output;
	}

	/**
	 * Renders an HTML drop-down box
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function dropdown( $args = array() ) {

		$defaults = array(
				'id'           => '',
				'name'         => 'select',
				'value'        => '',
				'label'        => '',
				'desc'         => '',
				'info'         => '',
				'placeholder'  => '',
				'class'        => 'epub-select',
				'current'      => null,
				'options'      => array()
		);
		$args = wp_parse_args( $args, $defaults );

		$output = '<label class="label" for="epub_' . $args['name'] . '">' . esc_html( $args['label'] ) . '</label>';

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$id = empty($args['name']) ? '' :  'id="' . esc_attr('epub_' . $args['name'] ) . '"';

		$output .= '<select ' . $id . ' class="' . $args['class'] . '" name="epub_' . esc_attr( $args['name'] ) . '">';

		foreach( $args['options'] as $key => $label ) {
			$selected = selected( $key, $args['current'], false );
			$output .= '<option value="' . esc_attr($key) . '"' . $selected . '>' . esc_html($label) . '</option>';
		}

		$output .= '</select>';

		if ( ! empty( $args['info'] ) ) {
			$output .= '<span class="info-icon"><p class="hidden">' . $args['info'] . '</p></span>';
		}

		return $output;
	}

	/**
	 * Renders several HTML radio buttons in a row
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function radio_buttons_horizontal( $args = array() ) {

		$output = '';
		$defaults = array(
			'id'           => 'radio',
			'name'         => 'radio-buttons',
			'value'        => '',
			'label'        => '',
			'title'        => '',
			'desc'         => '',
			'info'         => '',
			'class'        => 'epub-radio-button',
			'current'      => null,
			'options'      => array()
		);
		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$ix = 0;
		$output .= '<div class="radio-buttons-horizontal">';
			$output .= '<h4 class="label">'.esc_html( $args['label'] ).'</h4>';
			$output .= '<ul>';

				foreach( $args['options'] as $key => $label ) {
					$id = empty($args['name']) ? '' :  'id="' . esc_attr('epub_' . $args['name'] ).$ix . '"';
					$checked = checked( $key, $args['current'], false );

					$output .= '<li>';
						$output .= '<input type="radio"';
							$output .= ' '. $id.'" ';
							$output .= ' class="'. esc_attr( $args['class'] ).'" ';
							$output .= ' name="epub_' . esc_attr( $args['name'] ).'" ';
							$output .= ' value="'. esc_attr( $key ) . '" ';
							$output .= $checked;
					$output .= ' />';

					$output .= '<label';
						$output .= ' for="epub_'.  esc_attr( $args['name'] ).$ix.'" >';
						$output .= esc_html( $label );
					$output .= '</label>';


					$output .= '</li>';
					$ix++;
				}//foreach

			$output .= '</ul>';
			if ( ! empty( $args['info'] ) ) {
				$output .= '<span class="info-icon"><p class="hidden">' . ( is_array($args['info']) ? $args['info'][$ix] : $args['info'] ) . '</p></span>';
			}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders several HTML radio buttons in a column
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public function radio_buttons_vertical( $args = array() ) {
		$output = '';
		$defaults = array(
				'id'           => 'radio',
				'name'         => 'radio-buttons',
				'value'        => '',
				'label'        => '',
				'title'        => '',
				'desc'         => '',
				'info'         => '',
				'class'        => 'epub-radio-button',
				'current'      => null,
				'options'      => array()
		);
		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$ix = 0;
		$output .= '<div class="radio-buttons-vertical">';
			$output .= '<h4 class="label">'.esc_html( $args['label'] ).'</h4>';
			$output .= '<ul>';

			foreach( $args['options'] as $key => $label ) {
				$id = empty($args['name']) ? '' :  'id="' . esc_attr('epub_' . $args['name'] ).$ix . '"';
				$checked = checked( $key, $args['current'], false );

				$output .= '<li>';
					$output .= '<input type="radio"';
						$output .= ' '.$id.'" ';
						$output .= ' class="'. esc_attr( $args['class'] ).'" ';
						$output .= ' name="epub_' . esc_attr( $args['name'] ).'" ';
					    $output .= ' value="'. esc_attr( $key ) . '" ';
						$output .= $checked;
					$output .= ' />';

					$output .= '<label';
						$output .= ' for="epub_'.  esc_attr( $args['name'] ).$ix.'" >';
							$output .= esc_html( $label );
					$output .= '</label>';


				$output .= '</li>';
				$ix++;
			}//foreach

			$output .= '</ul>';
		if ( ! empty( $args['info'] ) ) {
			$output .= '<span class="info-icon"><p class="hidden">' . ( is_array($args['info']) ? $args['info'][$ix] : $args['info'] ) . '</p></span>';
		}
		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders two text fields. The second text field depends in some way on the first one
	 *
	 * @param $common_label - will show in front
	 * @param array $args1 - configuration for the first text field
	 * @param array $args2 - configuration for the second field
	 *
	 * @return string
	 */
	public function text_fields_horizontal( $common_label, $args1 = array(), $args2 = array() ) {

		$defaults = array(
			'id'           => '',
			'name'         => 'text',
			'value'        => '',
			'label'        => '',
			'desc'         => '',
			'info'         => '',
			'placeholder'  => '',
			'class'        => 'regular-text',
			'disabled'     => false,
			'autocomplete' => false,
			'data'         => false,
			'size'          => 3,
			'max'          => 50
		);
		$args1 = wp_parse_args( $args1, $defaults );
		$args2 = wp_parse_args( $args2, $defaults );

		$output = '<div class="text-fields-horizontal">';

		$output .= "<span class='common-label'>$common_label</span>";

		$output .= '<ul>';

		$output .= '<li>';
		// FIELD 1
		$id =  esc_attr('epub_' . $args1['name']);
		$autocomplete = ( $args1['autocomplete'] ? 'on' : 'off' );
		$disabled = $args1['disabled'] ? ' disabled="disabled"' : '';

		$data = '';
		if ( ! empty( $args1['data'] ) ) {
			foreach ( $args1['data'] as $key => $value ) {
				$data .= "data-$key=$value";
			}
		}

		$output .= "<span id='{$id}-wrap'>";
		$output .= "<label class='label' for='{$id}'>" . esc_html( $args1['label'] ) . "</label>";
		if ( ! empty( $args1['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args1['desc'] ) . '</span>';
		}
		$output .= "<input type='text' name='{$id}' id='{$id}' class='{$args1['class']}' autocomplete='$autocomplete' value='" . esc_attr( $args1['value'] ) .
		           "' placeholder='" . esc_attr( $args1['placeholder'] ) . "'" . $data . $disabled . " maxlength='{$args1['max']}' />";
		$output .= '</span>';
		$output .= '</li>';

		$output .= '<li>';
		// FIELD 2
		$id =  esc_attr('epub_' . $args2['name']);
		$autocomplete = ( $args2['autocomplete'] ? 'on' : 'off' );
		$disabled = $args2['disabled'] ? ' disabled="disabled"' : '';

		$data = '';
		if ( ! empty( $args2['data'] ) ) {
			foreach ( $args2['data'] as $key => $value ) {
				$data .= "data-$key=$value";
			}
		}

		$output .= "<span id='{$id}-wrap'>";
		$output .= "<label class='label' for='{$id}'>" . esc_html( $args2['label'] ) . "</label>";
		if ( ! empty( $args2['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args2['desc'] ) . '</span>';
		}
		$output .= "<input type='text' name='{$id}' id='{$id}' class='{$args2['class']}' autocomplete='$autocomplete' value='" . esc_attr( $args2['value'] ) .
		           "' placeholder='" . esc_attr( $args2['placeholder'] ) . "'" . $data . $disabled . " maxlength='{$args2['max']}' />";
		$output .= '</span>';
		$output .= '</li>';

		$output .= '</ul>';

		// HELP
		$help_text = $args1['info'] . ' ' . $args2['info'];
		if ( ! empty( $help_text ) ) {
			$output .= "<span class='info-icon'><p class='hidden'>{$help_text}</p></span>";
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders two text fields that related to each other. One field is text and other is select.
	 *
	 * @param bool $first_text - if true then sequence is TEXT - SELECT otherwise SELECT - TEXT
	 * @param $common_label - will show in front
	 * @param array $text_args
	 * @param array $select_args
	 *
	 * @return string
	 */
	public function text_and_select_fields_horizontal( $first_text=true, $common_label, $text_args = array(), $select_args = array() ) {

		// TEXT FIELD

		$defaults = array(
			'id'           => '',
			'name'         => 'text',
			'value'        => '',
			'label'        => '',
			'desc'         => '',
			'info'         => '',
			'placeholder'  => '',
			'class'        => 'regular-text',
			'disabled'     => false,
			'autocomplete' => false,
			'data'         => false,
			'size'          => 3,
			'max'          => 50
		);
		$text_args = wp_parse_args( $text_args, $defaults );

		$output_text = '<li>';

		$id =  esc_attr('epub_' . $text_args['name']);
		$autocomplete = ( $text_args['autocomplete'] ? 'on' : 'off' );
		$disabled = $text_args['disabled'] ? ' disabled="disabled"' : '';

		$data = '';
		if ( ! empty( $text_args['data'] ) ) {
			foreach ( $text_args['data'] as $key => $value ) {
				$data .= "data-$key=$value";
			}
		}

		$output_text .= "<span id='{$id}-wrap'>";
		$output_text .= "<label class='label' for='{$id}'>" . esc_html( $text_args['label'] ) . "</label>";
		if ( ! empty( $text_args['desc'] ) ) {
			$output_text .= '<span class="epub-description">' . esc_html( $text_args['desc'] ) . '</span>';
		}
		$output_text .= "<input type='text' name='{$id}' id='{$id}' class='{$text_args['class']}' autocomplete='$autocomplete' value='" . esc_attr( $text_args['value'] ) .
		                "' placeholder='" . esc_attr( $text_args['placeholder'] ) . "'" . $data . $disabled . " maxlength='{$text_args['max']}' />";
		$output_text .= '</span>';
		$output_text .= '</li>';


		// SELECT FIELD
		$defaults = array(
			'id'           => '',
			'name'         => 'select',
			'value'        => '',
			'label'        => '',
			'desc'         => '',
			'info'         => '',
			'placeholder'  => '',
			'class'        => 'epub-select',
			'current'      => null,
			'options'      => array()
		);
		$select_args = wp_parse_args( $select_args, $defaults );

		$output_select = '<li>';
		$output_select .= '<label class="label" for="epub_' . $select_args['name'] . '">' . esc_html( $select_args['label'] ) . '</label>';

		if ( ! empty( $select_args['desc'] ) ) {
			$output_select .= '<span class="epub-description">' . esc_html( $select_args['desc'] ) . '</span>';
		}

		$id = empty($select_args['name']) ? '' :  'id="' . esc_attr('epub_' . $select_args['name'] ) . '"';

		$output_select .= '<select ' . $id . ' class="' . $select_args['class'] . '" name="epub_' . esc_attr( $select_args['name'] ) . '">';

		foreach( $select_args['options'] as $key => $label ) {
			$selected = selected( $key, $select_args['current'], false );
			$output_select .= '<option value="' . esc_attr($key) . '"' . $selected . '>' . esc_html($label) . '</option>';
		}

		$output_select .= '</select>';
		$output_select .= '</li>';


		// ASSEMBLE FIELDS

		$output = '<div class="text-fields-horizontal">';

		$output .= "<span class='common-label'>$common_label</span>";

		$output .= '<ul>';

		$output .= $first_text ? $output_text . $output_select : $output_select . $output_text;

		$output .= '</ul>';


		// HELP
		$help_text = $first_text ? $text_args['info'] . ' ' . $select_args['info'] : $select_args['info'] . ' ' . $text_args['info'];
		if ( ! empty( $help_text ) ) {
			$output .= "<span class='info-icon'><p class='hidden'>{$help_text}</p></span>";
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Renders several HTML checkboxes in several columns
	 *
	 * @param array $args
	 * @param string $empty_list
	 *
	 * @return string
	 */
	public function checkboxes_multi_select( $args = array(), $empty_list = 'Not Available' ) {
		$output = '';
		$defaults = array(
			'id'           => 'checkbox',
			'name'         => 'checkbox',
			'value'        => array(),
			'label'        => '',
			'title'        => '',
			'desc'         => '',
			'info'         => '',
			'class'        => 'epub-checkbox',
			'main_class'   => '',
			'current'      => null,
			'options'      => array()
		);
		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="epub-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$ix = 0;
		$output .= '<div class="epub-checkboxes-vertical '.esc_attr( $args['main_class'] ).'">';
		$output .= '<h4 class="label">'.esc_html( $args['label'] ).'</h4>';
		if ( ! empty( $args['info'] ) ) {
			$output .= '<span class="info-icon"><p class="hidden">' . ( is_array($args['info']) ? $args['info'][$ix] : $args['info'] ) . '</p></span>';
		}
		$output .= '<ul>';

		if ( empty($args['options']) ) {
			$args['options'] = array();
			$output .= $empty_list;
		}

		foreach( $args['options'] as $key => $label ) {

			$tmp_value = is_array($args['value']) ? $args['value'] : array();

			if ( $args['type'] == EPUB_Input_Filter::CHECKBOXES_MULTI_SELECT_NOT ) {
				$checked = in_array($key, array_keys($tmp_value)) ? '' : 'checked';
			} else {
				$checked = in_array($key, array_keys($tmp_value)) ? 'checked' : '';
			}

			$input_attr = array(
				'id'    => empty($args['name']) ? '' :  ' id="' . esc_attr('epub_' . $args['name'] ).$ix . '"',
				'value' => ' value="'. esc_attr( $key . '[[-,-]]' . $label ) . '" ',
				'name'  => ' name="epub_' . esc_attr( $args['name'] ) . '_' . $ix . '" ',
				'class' => ' class="' . esc_attr( $args['class'] ).'" ',
				'checked' => $checked
			);

			$label = str_replace(',', '', $label);

			$output .= '<li>';
			if ( $args['type'] == EPUB_Input_Filter::CHECKBOXES_MULTI_SELECT_NOT ) {
				$output .= '<input type="hidden" value="'. esc_attr( $key . '[[-HIDDEN-]]' . $label ) . '" name="epub_' . esc_attr( $args['name'] ) . '_' . $ix . '">';
			}
			$output .= '<input type="checkbox"';
			$output .= $input_attr[ 'id' ];
			$output .= $input_attr[ 'class' ];
			$output .= $input_attr[ 'name' ];
			$output .= $input_attr[ 'value' ];
			$output .= $input_attr[ 'checked' ];
			$output .= ' />';

			$output .= '<label';
			$output .= ' for="epub_'.  esc_attr( $args['name'] ).$ix.'" >';
			$output .= esc_html( $label );
			$output .= '</label>';
			$output .= '</li>';
			$ix++;
		} //foreach

		$output .= '</ul>';

		$output .= '</div>';

		return $output;
	}

	/**
	 * Output submit button
	 *
	 * @param string $button_label
	 * @param string $action
	 * @param string $id
	 */
	public function submit_button( $button_label='Save', $action='epub_save_settings', $id='save_settings' ) {   ?>
		<div class="submit save_settings">
			<input type="hidden" id="_wpnonce_<?php echo $action; ?>" name="_wpnonce_<?php echo $action; ?>" value="<?php echo wp_create_nonce( "_wpnonce_$action" ); ?>"/>
			<input type="hidden" name="action" value="<?php echo $action; ?>"/>
			<input type="submit" class="primary-btn" value="<?php _e( $button_label, 'epub' ); ?>" />
		</div>  <?php
	}
}
