<?php

/**
 * Add Publish Menu to top admin bar
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPUB_Publish_Menu_Admin_Bar {

	// keep the name same
	const PUBLISH_MENU_ID = 'epub-publish-links';

	public function __construct() {
		add_action( 'admin_bar_init', array( $this, 'admin_bar_publish_menu_init' ) );
	}

	/**
	 * Add hooks so that when admin bar is being generated our feature is included.
	 */
	function admin_bar_publish_menu_init() {

		// show Publish Menu only on post and page screens including CPT
		if ( epub_is_option_on( 'show_publishing_menu' ) ) {
			add_action( 'admin_bar_menu', array( $this, 'add_admin_publish_menu'), 490 );
		}
	}

	/**
	 * Add publish menu to admin bar with links to update posts/pages
	 */
	function add_admin_publish_menu() {
		global $wp_admin_bar;

		// 1. add PUBLISH menu item
		$wp_admin_bar->add_menu( array(
			'title' => 'Publish',
			'href'  => false,
			'id'    => self::PUBLISH_MENU_ID
		) );

		// 2. add Publish submenu links.
		$wp_admin_bar->add_menu( array(
			'title'  => 'Update',
			'href'   => '#',
			'id'     => 'epub-update',
			'parent' => self::PUBLISH_MENU_ID
		) );
		$wp_admin_bar->add_menu( array(
			'title'  => 'Preview Changes',
			'href'   => '#',
			'id'     => 'epub-preview-changes',
			'parent' => self::PUBLISH_MENU_ID
		) );

		// 3. add the Save as links.
		$wp_admin_bar->add_menu( array(
			'title'  => 'Save Draft',
			'href'   => '#',
			'id'     => 'epub-save-draft',
			'parent' => self::PUBLISH_MENU_ID
		) );
		$wp_admin_bar->add_menu( array(
			'title'  => 'Save as Pending Review',
			'href'   => '#',
			'id'     => 'epub-save-as-pending-review',
			'parent' => self::PUBLISH_MENU_ID
		) );
	}
}
