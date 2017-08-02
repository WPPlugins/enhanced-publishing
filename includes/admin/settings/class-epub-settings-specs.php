<?php

/**
 * Collects feature specifications from each feature.
 */
class EPUB_Settings_Specs {

	/**
	 * Defines data needed for display, initialization and validation/sanitation of settings
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'optional' => 'true' )
	 *
	 * @return array with settings specification
	 */
	public static function get_fields_specification() {
		// all default settings are listed here
		$plugin_settings = array(

				// TAB: Posts and Pages Lookup

				'post_drop_down_on' => array(
						'label'       => 'Show Posts Lookup',
						'name'        => 'post_drop_down_on',
						'info'        => 'Whether to show a list of posts in the top admin bar. @ ' . Enhanced_Publishing::$plugin_url . 'img/settings/show-posts-lookup.jpg',
						'type'        => EPUB_Input_Filter::CHECKBOX,
						'default'     => 'on',
						'reload'      => true
				),
				'page_drop_down_on' => array(
						'label'       => 'Show Pages Lookup',
						'name'        => 'page_drop_down_on',
						'info'        => 'Whether to show a list of pages in the top admin bar. @ ' . Enhanced_Publishing::$plugin_url . 'img/settings/show-pages-lookup.jpg',
						'type'        => EPUB_Input_Filter::CHECKBOX,
						'default'     => 'on',
						'reload'      => true
				),
				'cpt_drop_down_on' => array(
						'label'       => 'Show Lookup For Custom Post Types',
						'name'        => 'cpt_drop_down_on',
						'info'        => 'Whether to show a list of custom post types in the top admin bar. @ ' . Enhanced_Publishing::$plugin_url . 'img/settings/show-cpts-lookup.jpg',
						'type'        => EPUB_Input_Filter::CHECKBOX,
						'default'     => 'on',
						'reload'      => true
				),
				'links_color' => array(
						'label'       => 'Color of Post/Page Links',
						'name'        => 'links_color',
						'info'        => 'Choose the color for links to posts and pages. @ ' . Enhanced_Publishing::$plugin_url . 'img/settings/color-post-page-links.jpg',
						'type'        => EPUB_Input_Filter::SELECTION,
						'options'     => array( 'blue-color' => 'Blue Color', 'theme-color' => 'Theme Color' ),
						'default'     => 'blue-color',
						'reload'      => true
				),
				'open_list_method' => array(
						'label'       => 'Hover or click to open posts/pages lookup',
						'name'        => 'open_list_method',
						'info'        => 'How to open Enhanced Publishing menu and lookups: user clicks or user hovers with mouse. Mobile screens use click only.',
						'type'        => EPUB_Input_Filter::SELECTION,
						'options'     => array( 'pointer-hover' => 'Hover', 'mouse-click' => 'Click' ),
						'default'     => 'pointer-hover',
						'reload'      => true
				),
				// while customers select CPTs to show, we record CPTs to not show (hide)
				'hidden_cpts' => array(
						'label'       => 'Shown CPTs',
						'name'        => 'hidden_cpts',
						'info'        => 'Select Custom Post Types that will appear in CPTs lookup in the top admin bar. <br/><br/>Note: if your WordPress has no Custom Post Types then this option will have none listed with the message "Not Available".
									@ ' . Enhanced_Publishing::$plugin_url . 'img/settings/shown-cpts.jpg',
						'type'        => EPUB_Input_Filter::CHECKBOXES_MULTI_SELECT_NOT,
						'default'     => array(),
						'optional'    => true,
						'reload'      => 'on'
				),

				// TAB: Publish Menu

				'show_publishing_menu' => array(
						'label'       => 'Show Publishing Menu',
						'name'        => 'show_publishing_menu',
						'info'        => 'Switch publish menu on/off. @ ' . Enhanced_Publishing::$plugin_url . 'img/settings/show-publish-menu.jpg',
						'type'        => EPUB_Input_Filter::CHECKBOX,
						'default'     => 'on',
						'reload'      => true
				)
		);
		return apply_filters( 'epub_settings_specs', $plugin_settings );
	}

	/**
	 * Get default settings of this plugin
	 *
	 * @return array contains default setting values
	 */
	public static function get_default_settings() {
		$setting_specs = self::get_fields_specification();
		if ( ! is_array($setting_specs) ) {
			return array();
		}

		$configuration = array();
		foreach( $setting_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$configuration += array( $key => $default );
		}

		return $configuration;
	}

	/**
	 * Get names of all configuration items for settings
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification() );
	}
}