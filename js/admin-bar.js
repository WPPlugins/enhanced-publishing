// for BOTH front-end and back-end display
jQuery(document).ready(function($) {

    /* -----------------  POSTS AND PAGES LOOKUP  ----------------------*/

    //Set the click functionality on mobile screen sizes
    var window_width = $( window ).width();
    if( window_width <= 782 ){
        $( '#wp-admin-bar-epub-post-links').addClass( 'epub-hide-lookup');
        $( '#wp-admin-bar-epub-post-links').addClass( 'epub-hover-effect');
    }

    epub_post_lookup_drop_down_display( '#wp-admin-bar-epub-post-links' );
    epub_post_lookup_drop_down_display( '#wp-admin-bar-epub-page-links' );
    epub_post_lookup_drop_down_display( '#wp-admin-bar-epub-cpt-links' );

    // hide Lookup menu when user clicks outside of it.
    function epub_click_outside_lookup(event) {
        // find ID of the closest object of the target click
        var $id = event.target.offsetParent ? event.target.offsetParent.id : '';

        if ( !$(event.target).closest('#wp-admin-bar-epub-post-links').length
              && !$(event.target).closest('#wp-admin-bar-epub-page-links').length
              && !$(event.target).closest('#wp-admin-bar-epub-cpt-links').length ) {
            $('#wp-admin-bar-root-default').find( 'li' ).removeClass('epub_active' );
            document.removeEventListener( "click", epub_click_outside_lookup);
        } else if ( $('#'+$id).hasClass('epub_active_done') ) {
            $('#'+$id).removeClass('epub_active_done');
        } else if ( $('#'+$id).hasClass('epub_active_done') === false  && ($id == 'wp-admin-bar-epub-post-links' || $id == 'wp-admin-bar-epub-page-links' || $id == 'wp-admin-bar-epub-cpt-links') ) {
            $('#wp-admin-bar-root-default').find( 'li' ).removeClass('epub_active' );
            document.removeEventListener( "click", epub_click_outside_lookup);
        }
    }

    // hide lookup when user hovers over other admin bar menu
    function epub_hover_event() {
        $('#wp-admin-bar-root-default').find( 'li' ).removeClass('epub_active' );
        $('#wpadminbar').find('li.menupop:not([id^="wp-admin-bar-epub"])').unbind("mouseenter.epub-bar");
    }

    function epub_post_lookup_drop_down_display( target_parent_id ){

        // 1. if user clicks on lookup menu show it
        $( target_parent_id ).on( 'click', function(){

            // if user wants to use hover instead of mouse click to open lookup return
            if ( $('#wpadminbar').find('.epub-hide-lookup').length == 0 ) {
                return;
            }

            if ( $(this).hasClass('epub_active') ) {
                return;
            }

            // Hide Lookup menu when user clicks outside of it or hovers over another menu
            document.addEventListener( "click", epub_click_outside_lookup );
            $('#wpadminbar').find('li.menupop:not([id^="wp-admin-bar-epub"])').bind("mouseenter.epub-bar", epub_hover_event);

            $( '#wp-admin-bar-root-default').find('> li').removeClass( 'epub_active' );
            $(this).addClass('epub_active');
            $(this).addClass('epub_active_done');
        });

        // 2. if user hovers on post state request records if needed
        $( target_parent_id + '-default > li > .ab-item' ).mouseenter( function(e) {
            var post_state_id = $( this).parent().attr( 'id' );

            $(this).toggleClass( 'epub_hover');

            var load_more = $( '#' +post_state_id+ ' .epub_load_more').length;
            if ( load_more ) {
                epub_load_more_posts( post_state_id );
            }
        });

        // 3. show records when user clicks on the post type
        $( target_parent_id + '-default > li > .ab-item' ).click( function(){

            // if user wants to use hover instead of mouse click to open lookup return
            if ( $('#wpadminbar').find('.epub-hide-lookup').length == 0 ) {
                return;
            }

            $( target_parent_id + '-default > li').removeClass( 'epub_active' );
            $(this).parent().addClass('epub_active');
        });

        // 4. show more records
        $( target_parent_id ).on( 'click', '.epub_show_more_message', function() {

            // if user wants to use hover instead of mouse click to open lookup return
            if ( $('#wpadminbar').find('.epub-hide-lookup').length == 0 ) {
                return;
            }

            var post_state_id = $( this ).attr( 'id' );
            epub_show_more_posts( post_state_id );
        });

        // 5a. if CPT Group Heading is clicked then run code
        $( 'body' ).on( 'click', '.cpt_group_heading', function(){

            // if user wants to use hover instead of mouse click to open lookup return
            if ( $('#wpadminbar').find('.epub-hide-lookup').length == 0 ) {
                return;
            }

            load_cpt_group.call( this );
        });
        // 5b. if CPT Group Heading is hovered over then run code
        $( 'body .cpt_group_heading' ).mouseenter( function(){

            // if user wants to use click instead of hover then exit
            if ( $('#wpadminbar').find('.epub-hide-lookup').length > 0 ) {
                return;
            }

            //Remove all other Third windows of CPT first
            $('.epub-third-box').remove();

            load_cpt_group.call( this );
        });

        function load_cpt_group() {
            if ( $(this).hasClass('epub_active') ) {
                return;
            }

            $( '.cpt_group_heading' ).removeClass( 'epub_active' );
            $( this ).addClass( 'epub_active' );

            //Load Third Window of CPT
            var post_state_id = $( this ).attr( 'id' );
            epub_load_cpt_posts( post_state_id );
        }
    }

    function epub_load_more_posts( post_state_id ){

        // spinner: $( '#' + post_state_id + ' .epub_load_more .ab-item').before( '<div class="loading-spinner"></div>');

        var postData = {
            action: 'epub_get_more_posts',
            info: post_state_id
        };

        //noinspection JSUnresolvedVariable
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: postData,
            url: epub_scripts.ajaxurl
        }).done(function (response)
        {
            response = ( response ? response : '' );
            if (response.success != undefined)
            {
                $( '#' + post_state_id + ' .epub_show_more_message').remove();

                var dataLen = response.success.length;
                for (var i = 0; i < dataLen; i++) {
                     $( '#'+post_state_id + ' ul').append( response.success[i] );
                }
            }
        }).always(function ()
        {
           $( '#'+post_state_id+ ' .epub_load_more').remove();
        });
    }

    function epub_show_more_posts( post_state_id ){

        $( '#' + post_state_id ).prepend( '<div class="loading-spinner"></div>');

        var postData = {
            action: 'epub_get_more_posts',
            info: post_state_id
        };

        //noinspection JSUnresolvedVariable
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: postData,
            url: epub_scripts.ajaxurl
        }).done(function (response)
        {
            response = ( response ? response : '' );
            if (response.success != undefined)
            {
                var dataLen = response.success.length;
                for (var i = 0; i < dataLen; i++) {
                    $( '#'+post_state_id).before( response.success[i] );
                }
            }
        }).always(function ()
        {
            $( '#'+post_state_id).remove();
        });
    }

    function epub_load_cpt_posts( post_state_id ){
        $( '#' + post_state_id ).prepend( '<div class="loading-spinner"></div>');

        var postData = {
            action: 'epub_get_more_posts',
            info: post_state_id
        };

        //noinspection JSUnresolvedVariable
        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: postData,
            url: epub_scripts.ajaxurl
        }).done(function (response)
        {
            response = ( response ? response : '' );
            if (response.success != undefined)
            {
                //Remove all other Third windows of CPT first
                $('.epub-third-box').remove();

                var dataLen = response.success.length;
                var $link = '';
                for (var i = 0; i < dataLen; i++) {
                    $link += response.success[i];
                }
                $( '#'+post_state_id + ' .ab-item').after( '<div class="epub-third-box ab-sub-wrapper"><ul>' + $link + '</ul></div>' );
                epub_fix_third_box_placement( post_state_id );
            }
        });
    }

    // When the scroll bar is visible the placement of the third box isn't placed accordingly
    function epub_fix_third_box_placement( $menuItem ){

       var $submenuWrapper = $( '#'+$menuItem + ' .epub-third-box' );

        // grab the menu item's position relative to its positioned parent
        var menuItemPos = $( '#'+$menuItem).position();

        //Set the new position of the third box only on desktop
        if( window_width > 782 ){
            $submenuWrapper.css({
                top: menuItemPos.top + 26,
                left: menuItemPos.left
            });
        }

    }

	/* -----------------  PUBLISH MENU  ----------------------*/

	(function() {

		//WordPress Default HTML Elements
		var $epub_default_publishButton = $('#publish');
		var $epub_default_postPreviewButton = $('#post-preview');
		var $epub_default_savePostButton = $('#save-post');
		var $epub_default_postStatus = $('#post_status');

		// feature elements added to admin bar
		var $epub_new_updateButton = $('#wp-admin-bar-epub-update').find('a');
		var $epub_new_previewChangesButton = $('#wp-admin-bar-epub-preview-changes').find('a');
		var $epub_new_draftButton = $('#wp-admin-bar-epub-save-draft').find('a');
		var $epub_new_pendingReviewButton = $('#wp-admin-bar-epub-save-as-pending-review').find('a');

		// if Publish button doesn't exists return
		if ( $epub_default_publishButton.length != 1 ) {
			return;
		}

		//Change Name of the Saving button depending on the state of the page/post
		var $epub_publishName = $epub_default_publishButton.val();
		$epub_new_updateButton.html($epub_publishName);

		//Save when Top Publish button is clicked
		$epub_new_updateButton.on('click', function (e)
		{
			e.preventDefault();

			epub_userPosition();

			//Run the Update button
			$epub_default_publishButton.trigger('click');
		});

		//Preview Changes
		$epub_new_previewChangesButton.on('click', function (e)
		{
			e.preventDefault();

			//Run the Update button
			$epub_default_postPreviewButton.trigger('click');
		});

		//If no Save Draft button is detected or Save as Pending
		if ($epub_default_savePostButton.length == 0)
		{

			//Rename Draft
			$epub_new_draftButton.html('Save As Draft');

			//Handle Saving as draft
			$epub_new_draftButton.on('click', function (e)
			{

				e.preventDefault();
				epub_userPosition();

				//Set the Post Status to Draft
				$("#post_status").val('draft');

				//Run the Publish Button
				$epub_default_publishButton.trigger('click');

			});

			//Handle Saving as Pending Review
			$epub_new_pendingReviewButton.on('click', function (e)
			{

				e.preventDefault();
				epub_userPosition();

				//Set the Post Status to Draft
				$("#post_status").val('pending');

				//Run the Publish Button
				$epub_default_publishButton.trigger('click');

			});

		}
		//Save As Button is detected ( Which is both Save Draft / Save as Pending )
		else if ($epub_default_savePostButton.length == 1)
		{

			//Get Status Value
			var $status = $epub_default_postStatus.val();

			//If Status as Pending
			if ($status == 'pending')
			{
				//Rename Draft
				$epub_new_draftButton.html('Save As Draft');
				//Rename Pending
				$epub_new_pendingReviewButton.html('Save Pending');
			}

			//Handle Saving as draft
			$($epub_new_draftButton).on('click', function (e)
			{
				e.preventDefault();
				epub_userPosition();

				//Set the Post Status to Draft
				$("#post_status").val('draft');

				//Run the Save Draft Button
				$epub_default_savePostButton.trigger('click');
			});

			//Handle Saving as Pending Review
			$epub_new_pendingReviewButton.on('click', function (e)
			{
				e.preventDefault();
				epub_userPosition();

				//Set the Post Status to Draft
				$("#post_status").val('pending');

				//Run the Publish Button
				$epub_default_savePostButton.trigger('click');
			});

		}

		$( window ).on( 'load' ,function(){

			//Get the Cookie Data
			var $cookieName     = 'epub_userPosition';
			var $cookieData     = document.cookie;
			var $startIndex     = $cookieData.indexOf( $cookieName );
			var $stringContent  = $cookieData.substring( $startIndex );
			var $endIndex       = $stringContent.indexOf( ";" );

			var $cookieRemainingString  = $stringContent.substring( 0, $endIndex );
			var $cookieValue            = $cookieRemainingString.substring( $cookieRemainingString.indexOf( "=" ) + 1 );

			//Set windows position
			window.scrollTo( 0, parseInt( $cookieValue ) );

			//Reset cookie
			document.cookie     = "epub_userPosition= 0";
		});

	})();

	/**
	 *  Retain User Scroll Position after page save.
	 *  Save current position as cookie data
	 */
	function epub_userPosition(){
		var $userPosition   = $( window ).scrollTop();
		document.cookie     = "epub_userPosition="+$userPosition;
	}

});
