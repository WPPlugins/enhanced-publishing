<?php

/**
 * Add links to admin bar
 *
 * @copyright   Copyright (C) 2016, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPUB_Posts_Pages_Lookup_Admin_Bar {

	const INITIAL_LOAD_LIMIT = 5;
	const NEXT_LOAD_LIMIT = 100;

	var $load_more = false;
	var $user_ID;
	var $seq_id = 0;
	var $post_statuses = array( 'all' => 'All', 'mine' => 'Mine', 'draft' => 'Draft', 'pending' => 'Pending',
	                                   'publish' => 'Published', 'future' => 'Scheduled', 'private' => 'Private' );

	public function __construct( $ajax=false ) {
		if ( $ajax ) {
			add_action( 'wp_ajax_epub_get_more_posts', array( $this, 'get_more_posts' ) );
		} else {
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_add_menu' ), 1000 );
		}
	}

	/**
	 * Add hooks so that when admin bar is being generated our feature is included.
	 */
	function admin_bar_add_menu() {

		if ( epub_is_option_on( 'post_drop_down_on' ) && current_user_can( 'edit_posts') ) {
			$this->add_sub_category_post_link( 'post', 'Posts' );
		}

		if ( epub_is_option_on( 'page_drop_down_on' ) && current_user_can( 'edit_pages') ) {
			$this->add_sub_category_post_link( 'page', 'Pages' );
		}

		if ( epub_is_option_on( 'cpt_drop_down_on' ) ) {
			$this->add_sub_category_post_link( 'cpt', 'CPTs' );
		}
	}

	/**
	 * Generate links for categories and subcategories of posts / pages
	 *
	 * @param $post_type - post | page | cpt
	 * @param $menu_name - Posts | Pages | CPTs
	 *
	 * @internal param array $args
	 */
	function add_sub_category_post_link( $post_type, $menu_name ) {
		global $wp_admin_bar;

		// add the top menu: Posts or Pages or CPTs
		$wp_admin_bar->add_menu( array(
			'title' => '<span class="ab-icon"></span><span class="ab-label">' . $menu_name . '</span>',
			'href'  => false,
			'id'    => 'epub-' . $post_type . '-links',
			'meta'  => array(
				'class' => epub_get_option_value('links_color') . ' ' .
				           ( epub_get_option_value('open_list_method') == 'mouse-click' ? 'epub-hide-lookup' : 'epub-hover-effect' )
			)
		) );

		// add items for each post type and post status combination
		$shown_cpts =  $post_type == 'cpt' ? $this->setup_shown_CPTs() : array();
		foreach( $this->post_statuses as $post_status => $post_status_name ) {

			$found_posts = $this->query_posts( $post_type, $post_status, 0, self::INITIAL_LOAD_LIMIT, true, $shown_cpts );

			// 1. add post/page type menu: All, Mine, Draft, Pending etc.
			$wp_admin_bar->add_menu( array(
				'title'  => $post_status_name . ( empty($found_posts) ? '&nbsp;&nbsp;&nbsp;<span class="epub_none">(none)</span>' : '' ),
				'href'   => false,
				'parent' => 'epub-' . $post_type . '-links', //Hook onto parent ID
				'id'     => 'epub-' . $post_type . '-' . $post_status . '-0'
			) );

			if ( empty( $found_posts ) ) {
				continue;
			}

			// 2. add group "Recently Modified"
			$wp_admin_bar->add_menu( array(
				'title'  => 'Recently Modified',
				'href'   => false,
				'parent' => 'epub-' . $post_type . '-' . $post_status . '-0',  //Hook onto parent ID
				'id'     => 'epub-' . $this->seq_id++,
				'meta'  => array( 'class' => 'group_heading')
			) );

			foreach ( $found_posts as $post ) {
				$can_edit = current_user_can( 'edit_post', $post->ID );
				// $post_title = ( empty($post->parent_post_title) ? '' : $post->parent_post_title . ' / ' ) . $post->post_title;
				$wp_admin_bar->add_menu( array(
					'title'  => EPUB_Utilities::adjust_post_title( $post->post_title ),
					'href'   =>	$can_edit ? get_edit_post_link( $post->ID ) : get_permalink( $post->ID ),
					'parent' => 'epub-' . $post_type . '-' . $post_status . '-0',  //Hook onto parent ID
					'id'     => 'epub-' . $this->seq_id++,
					'meta'  => array(
						'class' => $can_edit ? 'epub_edit' : 'epub_view',
						'title' => ( empty($post->parent_post_title) ? '' : 'Parent: ' . $post->parent_post_title . '&#10;&#10;' ) .
						           'Slug: ' . $post->post_name . '&#10;&#10;ID: ' . $post->ID
					)
				) );
			}

			// 3. add group "Alphabetical List"
			$wp_admin_bar->add_menu( array(
				'title'  => 'Alphabetical List',
				'href'   => false,
				'parent' => 'epub-' . $post_type . '-' . $post_status . '-0',  //Hook onto parent ID
				'id'     => 'epub-' . $this->seq_id++,
				'meta'  => array(
					'class' => 'group_heading')
			) );

			// display submenu for custom post types or post/pages
			if ( $post_type == 'cpt' ) {

				// display each group for this given CPT
				foreach ( $shown_cpts as $shown_cpt => $shown_cpt_name ) {

					if ( empty( $shown_cpt ) ) {
						continue;
					}

					$wp_admin_bar->add_menu( array(
						'title'  => $shown_cpt_name,
						'href'   => false,
						'parent' => 'epub-' . $post_type . '-' . $post_status . '-0',  //Hook onto parent ID
						'id'     => 'epub-' . $shown_cpt . '-' . $post_status . '-0',
						'meta'  => array( 'class' => 'cpt_group_heading')
					) );
				}

			} else {
				$post_type_status = $post_type . '-' . $post_status;
				$wp_admin_bar->add_menu( array(
					'title'  => 'Loading',
					'href'   => false,
					'parent' => 'epub-' . $post_type_status . '-0',  //Hook onto parent ID
					'id'     => $post_type_status . '-0',
					'meta'  => array( 'class' => 'epub_load_more' )
				) );
			}
		}
	}

	/**
	 * Query database for posts based on post type
	 *
	 * @param $post_type - post | page | cpt
	 * @param $post_status - mine | draft | pending | publish | future | private
	 * @param int $start_ix
	 * @param int $nof_recorders
	 * @param bool $sort_by_date - whether to order by post_modified or post_title
	 * @param array $shown_cpts
	 *
	 * @return array|null|object
	 * @internal param $args
	 */
	function query_posts( $post_type, $post_status, $start_ix, $nof_recorders, $sort_by_date=false, $shown_cpts=array() ) {
		global $wpdb;

		if ( empty($this->user_ID) ) {
			$this->user_ID = get_current_user_id();
		}

		$where = $post_type == 'cpt' ? "wp1.post_type IN ( '" . implode( "', '", esc_sql(array_keys($shown_cpts))) . "' )"
									 : "wp1.post_type = '{$post_type}'";

		$where .= ' AND ';
		switch( $post_status ) {
			case 'all':
				$where .= "wp1.post_status NOT IN ( 'auto-draft', 'trash', 'inherited' ) ";
				break;
			case 'mine':
				$where .= "wp1.post_author = {$this->user_ID} AND wp1.post_status NOT IN ( 'auto-draft', 'trash', 'inherited' ) ";
				break;
			default:
				$where .= "wp1.post_status = '{$post_status}' ";
		}

		$order_by = $sort_by_date ? 'wp1.post_modified DESC' : ( $post_type == 'cpt' ? 'wp1.post_type, wp1.post_title ASC' : 'wp1.post_title ASC' );

		// sort by children first, then parent-children pages
		// $order_by = $sort_by_date ? 'wp1.post_modified DESC' : ( $post_type == 'cpt' ? 'wp1.post_type, wp2.post_title IS NULL, wp1.post_title DESC, wp1.post_type, wp2.post_title, wp1.post_title ASC' :
		//	'wp2.post_title IS NULL DESC, wp2.post_title, wp1.post_title ASC' );

		// $where contains int and constants
		return $wpdb->get_results( $wpdb->prepare("SELECT wp1.ID, wp1.post_title, wp1.post_type, wp2.post_title AS parent_post_title, wp1.post_name " .
		                           "FROM $wpdb->posts wp1 LEFT JOIN $wpdb->posts wp2 " .
                                   " ON ( wp1.post_parent <> 0 AND wp1.post_parent = wp2.ID ) " .
		                           "WHERE $where " .
		                           "ORDER BY $order_by " .
		                           "LIMIT %d, %d ",
		                           $start_ix, $nof_recorders) );
	}

	/**
	 * Use to define the CPTs that will be shown
	 */
	private function setup_shown_CPTs() {

		$existing_post_types_objs = get_post_types( array( 'public'   => true, '_builtin' => false ), 'objects' );
		$existing_post_types = array();
		foreach( $existing_post_types_objs as $key => $value ) {
			$existing_post_types[$key] = empty($value->label) ? $key : $value->label;
		}

		$hidden_cpts = epub_get_instance()->settings_obj->get_value( 'hidden_cpts' );
		$hidden_cpts = is_array($hidden_cpts) ? $hidden_cpts : array();

		$shown_cpts = array_diff_key($existing_post_types, $hidden_cpts);
		$shown_cpts = array_diff($shown_cpts, array('page', 'post', 'attachment', 'nav_menu_item', 'revision'));

		return $shown_cpts;
	}

	/**
	 * Triggered when user opens lookup 
	 */
	public function get_more_posts() {

		// we don't need to check nonce here as we are retrieving links only

		$info = $_POST['info'];
		if ( empty($info) ) {
			return null;
		}
		$info = sanitize_text_field( $info );

		$info = str_replace( 'wp-admin-bar-epub-', '', $info );

		$pieces = explode('-', $info);
		if ( empty($pieces) || count($pieces) < 3 ) {
			return null;
		}

		// retrieve and sanitize the query parameters
		$next_seq_id = EPUB_Utilities::filter_number(array_pop($pieces));
		$next_seq_id = empty($next_seq_id) ? 0 : $next_seq_id;
		$post_status = array_pop($pieces);
		$post_type = implode('-', $pieces);

		if ( empty($post_type) || empty($post_status) ) {
			return null;
		}

		if ( ! in_array($post_type, array('post', 'page', 'cpt')) && ! in_array($post_type, array_keys($this->setup_shown_CPTs())) ) {
			return null;
		}

		if ( ! in_array($post_status, array_keys($this->post_statuses)) ) {
			return null;
		}

		$ajax_posts = $this->retrieve_next_ajax_posts( $post_type, $post_status, $next_seq_id );

		// we are done here
		wp_die( json_encode( array('success' => $ajax_posts) ) );
	}

	/**
	 * Get ids of next set of all posts/pages SORTED ALPHABETICALLY
	 *
	 * @param $post_type
	 * @param $post_status
	 * @param $next_seq_id - what records to start with
	 *
	 * @return array|null
	 */
	private function retrieve_next_ajax_posts( $post_type, $post_status, $next_seq_id ) {

		$posts_info = array();

		// get the next batch of records
		$found_posts = $this->query_posts( $post_type, $post_status, $next_seq_id, (self::NEXT_LOAD_LIMIT+1) );
		if ( empty($found_posts) || ! is_array($found_posts) ) {
			if ( $next_seq_id == 0 ) {
				$posts_info[] = '<span class="epub_none">(none)</span>';
				return $posts_info;
			} else {
				return null;
			}
		}

		// do we need link to load more?
		$load_more = count($found_posts) > self::NEXT_LOAD_LIMIT;
		if ( $load_more ) {
			array_pop( $found_posts );
		}

		foreach( $found_posts as $post ) {
			$url = current_user_can( 'edit_post', $post->ID ) ? get_edit_post_link( $post->ID ) : get_permalink( $post->ID );
			$class = current_user_can( 'edit_post', $post->ID ) ? 'epub_edit' : 'epub_view';
			// $post_title = ( empty($post->parent_post_title) ? '' : $post->parent_post_title . ' / ' ) . $post->post_title;
			$tooltip = ( empty($post->parent_post_title) ? '' : 'Parent: ' . $post->parent_post_title . '&#10;&#10;' ) .
			           'Slug: ' . $post->post_name . '&#10;&#10;ID: ' . $post->ID;
			$posts_info[] = '<li class="' . $class . '"><a class="ab-item" href="' . $url . '" title="' . esc_html($tooltip) . '"">' .
			                esc_html( EPUB_Utilities::adjust_post_title( $post->post_title ) ) . '</a></li>';
		}

		if ( $load_more ) {
			$id = 'wp-admin-bar-epub-' . esc_html($post_type) . '-' . $post_status . '-' . ($next_seq_id + self::NEXT_LOAD_LIMIT);
			$posts_info[] = '<li id="' . $id . '" class="epub_show_more_message">(Show More)</li>';
		}

		return $posts_info;
	}
}
