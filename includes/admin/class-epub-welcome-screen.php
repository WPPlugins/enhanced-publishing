<?php

/**
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPUB_Welcome_Screen {

	public function __construct( $do_redirect ) {
		if ( $do_redirect ) {
			add_action( 'admin_init', array( $this, 'setup_welcome' ), 20 );
		} else {
			add_action( 'admin_menu', array( $this, 'admin_menus') );
		}
	}

	/**
	 * Trigger display of welcome screen on plugin first activation or upgrade
	 */
	public function setup_welcome() {

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=epub-welcome-page' ) ); exit;
	}

	/**
	 * Register weclome page
	 */
	public function admin_menus() {
		// About Page
		$welcome_page = add_dashboard_page( 'Welcome to Enhanced Publishing', 'Welcome to Enhanced Publishing', 'manage_options', 'epub-welcome-page', array( $this, 'show_welcome_page' ) );
		if ( $welcome_page === false ) {
			return;
		}

		// load scripts needed for Features Settings page only on that page
		add_action( 'load-' . $welcome_page, array( $this, 'load_admin_page_resources') );

		// do not show this page in the WP menu itself
		remove_submenu_page( 'index.php', 'epub-welcome-page' );
	}

	/**
	 * Show the Welcome page
	 */
	public function show_welcome_page() {

		$plugin_just_installed = get_transient( '_epub_plugin_just_installed' );
		$plugin_just_installed = ! empty($plugin_just_installed);
		delete_transient( '_epub_plugin_just_installed' );
		$news_tab_active = $plugin_just_installed ? '' : 'active';
		$start_tab_active = $plugin_just_installed ? 'active' : '';

		// this is coming from settings page
		if ( isset( $_REQUEST['tab']) &&  $_REQUEST['tab'] == 'get-started' ) {
			$news_tab_active = '';
			$start_tab_active = 'active';
		}     ?>

		<div class="wrap">
			<div id="epub_admin_wrap">

				<!-- WELCOME HEADER -->
				<div class="welcome_header">
					<div class="row">
						<div style="width: 632px; float: left">
							<h1>Welcome to Enhanced Publishing <?php echo Enhanced_Publishing::$version; ?></h1>
							<p>            <?php
								echo $this->show_top_section( $plugin_just_installed ); ?>
							</p>
						</div>
						<div>
							<div class="logo">
								<img src="<?php echo Enhanced_Publishing::$plugin_url . 'img/epub-icon-shadow.png'; ?>">
							</div>
						</div>

					</div>
				</div>

				<!-- TABS -->
				<h2 id="welcome_tab_nav" class="nav-tab-wrapper">
					<a href="#" class="nav_tab nav-tab <?php echo $news_tab_active; ?>">What's New</a>
					<a href="#" class="nav_tab nav-tab <?php echo $start_tab_active; ?>">Getting Started</a>
				</h2>

				<!-- TAB CONTENT -->
				<div id="welcome_panel_container" class="panel-container">

					<!-- WHAT IS NEW -->
					<div class="tab-panel <?php echo $news_tab_active; ?>">  <?php
						echo $this->show_what_is_new();   ?>
					</div>

					<!-- GET STARTED -->
					<div class="tab-panel <?php echo $start_tab_active; ?>">  <?php
						echo $this->show_getting_started();   ?>
					</div>

				</div>

			</div><!-- epub_admin_wrap -->
		</div><!-- wrap -->
		<?php
	}

	private function show_top_section( $plugin_just_installed ) {

		// show welcome for newly installed plugin
		if ( $plugin_just_installed ) {
			return '
				Thanks for installing Enhanced Publishing plugin. To get started, read over the
				<a href="https://www.echoplugins.com/documentation/enhanced-publishing/" target="_blank">documentation</a>
				and play with the settings.';

			// text about quick tour of menus/settings locations
		} else {
			return '
				Thanks for using Enhanced Publishing. You can read about our new features
				 below or explore new <a href="' . admin_url('options-general.php?page=epub-settings') . '">settings</a>.';
		}
	}

	private function show_what_is_new() {
		return '
			<div class="row">
				<div class="col col_20">
					<h3>Choose the Color for Links to Posts and Pages</h3>
					<p>Links to posts/pages/cpts in lookups can have one of two colors: <strong style="color:blue;">blue</strong> or <strong>theme color</strong>.</p>
				</div>
				<div class="col col_30">
					<img src="' . Enhanced_Publishing::$plugin_url . 'img/settings/color-post-page-links.jpg">
				</div>
			</div>
			<div class="row">
				<div class="col col_20">
					<h3>See ID, Slug and Parent Name of Any Post</h3>
					<p>Hover over a post link to see the post <strong>slug</strong>, <strong>ID</strong> and <strong>parent name</strong> (if any).</p>
				</div>
				<div class="col col_30">
					<img src="' . Enhanced_Publishing::$plugin_url . 'img/welcome/post_info.jpg">
				</div>
			</div>

			<h2>Additional Updates</h2>
			<div class="row">
				<div class="col col_20">
					<h3>Choose How To View Lookups</h3>
					<p>New settings allow you to choose between <strong>clicking on</strong> and <strong>hovering over</strong> post/page/cpts lookups to open them.</p>
				</div>
				<div class="col col_20">
					<h3>Revamped Settings Page</h3>
					<p>We have fixed some layout issues and moved Enhanced Publishing settings into WordPress Settings menu <a href="' . admin_url('options-general.php?page=epub-settings') . '">here</a>.</p>
				</div>
			</div>
			<div class="row">
				<div class="col col_20">
					<h3>New Feedback Tab</h3>
					<p>We have added a form so that you can give us feedback without leaving the WordPress admin.</p>
				</div>
				<div class="col col_20">
					<h3>New About us Tab</h3>
					<p>Check out our other great plugins.</p>
				</div>
			</div>';
	}

	private function show_getting_started() {
		return '
			<div class="row">
				<div class="col col_20">
					<h3>Posts and Pages Lookup</h3>
					<p>In the top admin bar, you will see three new drop downs menus: Posts, Pages and CPTs (Custom Post Types).</p>
					<p>Use the lookups to quickly find any post/page/cpt.</p>

					<div class="col col_20">
						<a href="' . admin_url('options-general.php?page=epub-settings') . '">Settings</a>
					</div>
					<div class="col col_20">
						<a href="http://www.echoplugins.com/documentation/enhanced-publishing/posts-and-pages-lookup/" target="_blank">Documentation</a>
					</div>
				</div>
				<div class="col col_30">
					<img src="' . Enhanced_Publishing::$plugin_url . 'img/welcome/post_pages_cpts_lookup.jpg">
					<p>Lookup menus in the admin bar.</p>
				</div>
			</div>

			<div class="row">
				<div class="col col_20">
					<h3>Publish Menu<br/>(Visible On Post/Page Edit Screen)</h3>
					<p>A new Publishing menu appears in the admin bar when you open a post or page for edit.</p>
					<p>After you finish updating the post or page, click on the \'Update\' or \'Save as Draft\' in the Publishing menu to save your work.</p>
					<p>The menu is always accessible from the admin bar even if you are father down the page. You don\'t need to scroll back up to find the WordPress publish button.</p>
						 
					<div class="col col_20">
						<a href="' . admin_url('options-general.php?page=epub-settings') . '">Settings</a>
					</div>
					<div class="col col_20">
						<a href="http://www.echoplugins.com/documentation/enhanced-publishing/publishing-menu/" target="_blank">Documentation</a>
					</div>
				</div>
				<div class="col col_45">
					<img src="' . Enhanced_Publishing::$plugin_url . 'img/welcome/publishing_menu.jpg">
					<p>Publishing menu appears in the admin bar when you open a post or page for edit.</p>
				</div>
			</div>

			<h2>Need Help?</h2>
			<div class="row">
				<div class="col col_20">
					<h3>Documentation</h3>
					<p>Reference our knowledge base as it covers all the plugin features.</p>
					<a class="button primary-btn" href="http://www.echoplugins.com/documentation/enhanced-publishing/" target="_blank">Knowledge Base</a>
				</div>
				<div class="col col_20">
					<h3>Still Need Some Help?</h3>
					<p>If you encounter an issue or have a question, please submit your request below.</p>
					<a class="button primary-btn" href="https://www.echoplugins.com/contact-us/?inquiry-type=technical&plugin_type=enhanced-publishing" target="_blank">Contact Us</a>
				</div>
			</div>';
	}

	function load_admin_page_resources() {
		add_action( 'admin_enqueue_scripts', 'epub_load_admin_pages_resources' );
	}
}
