<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display feature settings
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
function epub_display_features_settings_page() {

	ob_start();  ?>

	<div class="wrap">
		<div id="epub_admin_wrap" class="epub_admin_wrap">

			<div id="top-notice-message"><a hidden id="top"></a></div>   <?php

			$header_option = get_option('epub_show_welcome_header');
			if ( ! empty($header_option) ) { ?>

			<div class="welcome_header">
				<div class="row">

					<div style="width: 675px; float: left">
						<h1>Welcome to Enhanced Publishing <?php echo Enhanced_Publishing::$version; ?></h1>
						<p>
							Thanks for using Enhanced Publishing. To get started, read over the
							<a href="https://www.echoplugins.com/documentation/enhanced-publishing/" target="_blank">documentation</a>
							and play with the settings. If you enjoy this plugin please consider telling a friend, or rating it
							<a href="https://wordpress.org/support/plugin/enhanced-publishing/reviews/?filter=5" target="_blank">5-stars</a>.
						</p>
					</div>
					<div style="float: left;">
						<div class="logo">
							<img src="<?php echo Enhanced_Publishing::$plugin_url . 'img/epub-icon-shadow.png'; ?>">
						</div>
						<button id="close_intro">Close</button>
					</div>

				</div>
			</div>

			<?php } ?>

			<?php epub_display_page_details();    ?>

		</div>
	</div>  <?php

	echo ob_get_clean();
}

/**
 * Display all configuration fields
 */
function epub_display_page_details() {

	$form = new EPUB_HTML_Elements();   ?>

	<form method="post" action="#">

		<!-- <section class="save-settings">  <?php
			$form->submit_button();   ?>
		</section> -->

		<div id="epub-tabs" class="settings_container">

			<!--  NAVIGATION TABS  -->

			<section class="main-nav">

				<ul class="nav-tabs">
					<li class="nav_tab active">
						<h2>Posts and Pages Lookup</h2>
						<p>Easily Switch to Any Post or Page</p>
					</li>
					<li class="nav_tab">
						<h2>Publish Menu</h2>
						<p>Easily publish posts or pages</p>
					</li>
					<li class="nav_tab">
						<h2>Help | Info</h2>
						<p>Docs / Contact Us / About Us</p>
					</li>
				</ul>

			</section>

			<!--  TABS CONTENT  -->

			<div id="main_panels" class="panel-container">

				<!--   FEATURES SETTINGS   --->     <?php

				$feature_specs = EPUB_Settings_Specs::get_fields_specification();
				$feature_settings = epub_get_instance()->settings_obj->get_settings();

				// we need CPT names
				$available_cpts_objs = get_post_types( array( 'public'   => true, '_builtin' => false ), 'objects' );
				$available_cpts = array();
				foreach( $available_cpts_objs as $key => $value ) {
					$available_cpts[$key] = empty($value->label) ? $key : $value->label;
				}	?>

				<!-- TAB: Posts and Pages Lookup -->

				<div class="tab-panel active">
					<section class="form-options">
						<ul>
							<li class="option-heading"><h4>Visibility of Lookup in Admin Bar</h4></li>
							<li class="config_item">
								<?php echo $form->checkbox( $feature_specs['post_drop_down_on'] + array( 'value' => $feature_settings['post_drop_down_on'] ) ); ?>
							</li>
							<li class="config_item">
								<?php echo $form->checkbox( $feature_specs['page_drop_down_on'] + array( 'value' => $feature_settings['page_drop_down_on'] ) ); ?>
							</li>
							<li class="config_item">
								<?php echo $form->checkbox( $feature_specs['cpt_drop_down_on'] + array( 'value' => $feature_settings['cpt_drop_down_on'] ) ); ?>
							</li>
							<li class="config_item">
								<?php echo $form->radio_buttons_horizontal(
										$feature_specs['links_color'] +
										array(
												'value'     => $feature_settings['links_color'],
												'current'   => $feature_settings['links_color']
										)
								); ?>
							</li>
							<li class="config_item">
								<?php echo $form->radio_buttons_horizontal(
										$feature_specs['open_list_method'] +
										array(
												'value'     => $feature_settings['open_list_method'],
												'current'   => $feature_settings['open_list_method']
										)
								); ?>
							</li>
							<li class="config_item">
								<?php echo $form->checkboxes_multi_select( $feature_specs['hidden_cpts'] + array( 'value' => $feature_settings['hidden_cpts'],
								                                                                                  'options' => $available_cpts, 'main_class' => 'checkbox-columns', 'No Custome Post Types Available' ) ); ?>
							</li>
						</ul>
					</section>

					<section class="save-settings">	<?php
						$form->submit_button();  ?>
					</section>

				</div>

				<!-- TAB: Publishing Menu -->

				<div class="tab-panel">
					<section class="form-options">
						<ul>
							<li class="option-heading"><h4>Publishing Menu Visibility</h4></li>
							<li class="config_item">
								<?php echo $form->checkbox( $feature_specs['show_publishing_menu'] + array( 'value' => $feature_settings['show_publishing_menu'], 'class' => 'med_input' ) ); ?>
							</li>
						</ul>
					</section>

					<section class="save-settings">	<?php
						$form->submit_button();  ?>
					</section>

				</div>

				<!--   HELP AND OTHER INFO -->

				<div class="tab-panel">

					<section class="sub_nav">
						<ul id="help_tabs_nav" class="nav-tabs">
							<li class="nav_tab">
								<h2>Help</h2>
							</li>
							<li class="nav_tab">
								<h2>Feedback</h2>
							</li>
							<li class="nav_tab active">
								<h2>About Us</h2>
							</li>
						</ul>
					</section>

					<div id="help_tab_panel" class="panel-container">

						<div class="tab-panel">
							<div class="row">
								<section class="col col_20">
									<h3>Getting Started / What's New</h3>
									<p>Overview of the plugin features, and what is new.</p>
									<a class="button primary-btn" href="<?php echo admin_url(); ?>/index.php?page=epub-welcome-page&tab=get-started">Quick Overview</a>
								</section>
								<section class="col col_20">
									<h3>Full Documentation</h3>
									<p>Knowledge base that explains all plugin features.</p>
									<a class="button primary-btn" href="https://www.echoplugins.com/documentation/enhanced-publishing/" target="_blank">Knowledge Base</a>
								</section>
								<section class="col col_20">
									<h3>Still Need Some Help?</h3>
									<p>If you encounter an issue or have a question, please submit your request below.</p>
									<a class="button primary-btn" href="https://www.echoplugins.com/contact-us/?inquiry-type=technical&plugin_type=enhanced-publishing" target="_blank">Contact us</a>
								</section>
							</div>
						</div>

						<div class="tab-panel">
							<div class="form-options">

								<section>
									<h3 style="padding-bottom: 20px;">What features should we add or improve?</h3>
									<ul>
										<li class="config_item">   <?php
											$params = array(
												'label'       => "Your Email (optional)",
												'name'        => 'your_email',
												'info'        => 'If you would like to hear back from us please provide us your email.',
												'type'        => EPUB_Input_Filter::TEXT,
												'optional'    => 'true',
												'max'         => '50',
												'min'         => '3',
												'class' => 'epub-large'
											);
											echo $form->text( $params ); ?>
										</li>
										<li class="config_item">   <?php
											$params = array(
												'label'       => "Your Ideas and Feedback *",
												'name'        => 'your_feedback',
												'info'        => '',
												'type'        => EPUB_Input_Filter::TEXT,
												'optional'    => 'false',
												'max'         => '1000',
												'min'         => '3',
												'class'       => 'lrg_input',
												'rows'        => 7
											);
											echo $form->textarea( $params ); ?>
										</li>
									</ul>
								</section>

								<div id='epub-ajax-in-progress-feedback' style="display:none;">
									Sending your feedback... <img class="epub-ajax waiting" style="height: 30px;" src="<?php echo Enhanced_Publishing::$plugin_url . 'img/loading_spinner.gif'; ?>">
								</div>

								<section style="padding-top: 20px;" class="save-settings">	<?php
									$form->submit_button( 'Send Feedback', 'epub_send_feedback', 'send_feedback' );  ?>
								</section>

							</div>
						</div>

						<div class="tab-panel active">
							<section>
								<h3 style="font-size: 20pt;">Our other Plugins</h3>

								<div class="preview_product">
									<div class="top_heading">
										<h4>Show IDs</h4>
									</div>
									<div class="featured_img">
										<img src="<?php echo 'http://www.echoplugins.com/wp-content/uploads/2016/10/show_id_plugin.png'; ?>">
									</div>
									<div class="description">
										<p>
											<span style="font-size: 14pt;"><strong>Show IDs</strong> of post, pages and taxonomies.</span>
										</p>
									</div>
									<a class="button primary-btn" href="https://www.echoplugins.com/wordpress-plugins/show-ids/" target="_blank">Learn More</a>

								</div>
								<div class="preview_product">
									<div class="top_heading">
										<h4>Content Down Arrow</h4>
									</div>
									<div class="featured_img">
										<img src="<?php echo 'http://www.echoplugins.com/wp-content/uploads/2016/10/arrow_plugin.png'; ?>">
									</div>
									<div class="description">
										<p>
											<span style="font-size: 14pt;">Display <strong>downward-pointing arrow</strong> to indicate more content below.</span>
										</p>
									</div>
									<a class="button primary-btn" href="https://www.echoplugins.com/wordpress-plugins/content-down-arrow/" target="_blank">Learn More</a>

								</div>
								<div class="preview_product">
									<div class="top_heading">
										<h4>Desk</h4>
									</div>
									<div class="featured_img">
										<img src="<?php echo 'http://www.echoplugins.com/wp-content/uploads/2016/10/desk_plugin.png'; ?>">
									</div>
									<div class="description">
										<p>
											<span style="font-size: 14pt;">Keep track of your WordPress work with <strong>checklists</strong>, <strong>bookmarks</strong> and more.</span>
										</p>
									</div>
									<a class="button primary-btn" href="https://www.echoplugins.com/wordpress-plugins/desk/" target="_blank">Learn More</a>
								</div>

							</section>
						</div>

					</div>

				</div>

			</div>
			
		</div>

		<div id='epub-ajax-in-progress' style="display:none;">
			Saving settings... <img class="epub-ajax waiting" style="height: 30px;" src="<?php echo Enhanced_Publishing::$plugin_url . 'img/loading_spinner.gif'; ?>">
		</div>

	</form>

	<div id="epub-dialog-info" title="">
		<p id="epub-dialog-msg"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span></p>
	</div>

	<div id="epub-dialog-info-icon" title="" style="display: none;">
		<p id="epub-dialog-info-icon-msg"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span></p>
	</div>  <?php
}
