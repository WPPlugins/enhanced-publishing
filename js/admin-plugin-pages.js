jQuery(document).ready(function($) {


    var epub = $( '#epub_admin_wrap' );

    // TABS
    //====================

    (function(){

        /**
         * Toggles Tabs
         *
         * The HTML Structure for this is as follows:
         * 1. tab_nav_container must be the main ID or class element for the navigation tabs containing the tabs.
         *    Those nav items must have a class of nav_tab.
         *
         * 2. tab_panel_container must be the main ID or class element for the panels. Those panel items must have
         *    a class of tab-panel
         *
         * @param tab_nav_container  ( ID/class containing the Navs )
         * @param tab_panel_container ( ID/class containing the Panels
         */
        function tab_toggle( tab_nav_container, tab_panel_container ){

            epub.find( tab_nav_container+ ' > .nav_tab' ).on( 'click', function(){

                //Remove all Active class from Nav tabs
                epub.find(tab_nav_container + ' > .nav_tab').removeClass('active');

                //Add Active class to clicked Nav
                $(this).addClass('active');

                //Remove Class from the tab panels
                epub.find(tab_panel_container + ' > .tab-panel').removeClass('active');

                //Set Panel active
                var number = $(this).index() + 1;
                epub.find(tab_panel_container + ' > .tab-panel:nth-child( ' + number + ' ) ').addClass('active');

            });

        }

        tab_toggle( '#welcome_tab_nav' , '#welcome_panel_container' );
        tab_toggle( '.main-nav > .nav-tabs', '#main_panels' );
        tab_toggle( '#help_tabs_nav', '#help_tab_panel' );

    })();

    // SAVE SETTINGS page
    //====================

    epub.find( '.save_settings' ).on('click', function (e)
    {
        e.preventDefault();  // do not submit the form
        var msg = '';

        var postData = {
            action: 'epub_save_settings',
            _wpnonce_epub_save_settings: $('#_wpnonce_epub_save_settings').val(),
            form: $('form').serialize()
        };

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: postData,
            url: ajaxurl,
            beforeSend: function (xhr)
            {
                $('#epub-ajax-in-progress').dialog('open');
            }

        }).done(function (response)
        {
            response = ( response ? response : '' );
            if (response.message != undefined)
            {
                msg = response.message;

                if (msg.indexOf('reload') >= 0)
                {
                    msg = msg.replace('reload ', '');
                    epub.find('#top-notice-message').html(msg);
                    msg = '';
                    $("html, body").animate({scrollTop: 0}, "slow");

                    window.setTimeout(show_reload_dialog, 2000);
                    function show_reload_dialog() {
                        location.reload();
                    }
                }
            } else {
                msg = epub_admin_notification('', response.error ? response.error : 'Please reload the page and try again (b1).', 'error');
            }

        }).fail(function (response, textStatus, error)
        {
            msg = ( error ? ' [' + error + ']' : 'unknown error' );
            msg = epub_admin_notification('Error occurred - configuration NOT saved. Please try again later', msg, 'error');
        }).always(function ()
        {
            $('#epub-ajax-in-progress').dialog('close');

            if ( msg ) {
                epub.find('#top-notice-message').html(msg);
                $("html, body").animate({scrollTop: 0}, "slow");
            }
        });
    });


    // SEND FEEDBACK
    //====================

    epub.find( '#send_feedback' ).on('click', function (e)
    {

        epub.find('.required').remove();

        e.preventDefault();  // do not submit the form
        var msg = '';
        var feedback = epub.find( '#your_feedback' ).val();

        if ( ! feedback ) {
            epub.find( '#your_feedback' ).after('<p class="required notification"><span class="error">* Feedback message is required</span> </p>');
            return;
        }

        var postData = {
            action: 'epub_send_feedback',
            _wpnonce_epub_send_feedback: $('#_wpnonce_epub_send_feedback').val(),
            feedback: feedback,
            email: epub.find( '#your_email' ).val()
        };

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: postData,
            url: ajaxurl,
            beforeSend: function (xhr)
            {
                $('#epub-ajax-in-progress-feedback').dialog('open');
            }

        }).done(function (response)
        {
            response = ( response ? response : '' );
            if (response.message != undefined)
            {
                msg = response.message;
            } else {
                msg = epub_admin_notification('', response.error ? response.error : 'Please reload the page and try again (b1).', 'error');
            }

        }).fail( function ( response, textStatus, error )
        {
            msg = ( error ? ' [' + error + ']' : 'unknown error' );
            msg = epub_admin_notification( 'Error occurred - feedback NOT sent. Please try again later', msg, 'error' );
        }).always(function ()
        {
            $( '#epub-ajax-in-progress-feedback' ).dialog( 'close' );

            if ( msg ) {
                epub.find( '#top-notice-message' ).html(msg);
                $( "html, body" ).animate( {scrollTop: 0}, "slow" );
            }
        });
    });


    // hide welcome section on settings page
    //======================================
    epub.find( '#close_intro' ).on( 'click', function() {

        epub.find( '.welcome_header' ).hide();

        var postData = {
            action: 'epub_close_welcome_header'
        };

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: postData
        })
    });


    /********************************************************************************************
     *
     *                                     DIALOGS
     *
     ********************************************************************************************/

    // SHOW INFO MESSAGES
    //====================

    function epub_admin_notification( $title, $message , $type ) {
        return '<div class="notification">' +
                    '<span class="' + $type + '">' +
                        ($title ? '<h4>'+$title+'</h4>' : '' ) +
                        ($message ? $message : '') +
                    '</span>' +
                '</div>';
    }

    $( '#epub-dialog-info' ).dialog({
        resizable: false,
        height: 250,
        width: 400,
        modal: true,
        autoOpen: false,
        buttons: {
            Ok: function ()
            {
                $( this ).dialog( "close" );
                var msg = $( this ).find( '#epub-dialog-msg' ).html();
                if ( msg.indexOf( 'reload' ) > 0 || msg.indexOf( 'refresh' ) > 0 )
                {
                    location.reload();
                }
            }
        }
    }).hide();


    // SETTINGS HELP DIALOG (image and text)
    //==============================================

    // open dialog but re-center when loading finished so that it stays in the center of the screen
    var epub_help_dialog = $( "#epub-dialog-info-icon" ).dialog(
        {
            resizable: false,
            autoOpen: false,
            modal: true,
            buttons: {
                Ok: function ()
                {
                    $( this ).dialog( "close" );
                }
            },
            close: function ()
            {
                $( '#epub-dialog-info-icon-msg' ).html();
            }
        }
    );
    epub.find( '.info-icon' ).on('click', function ()
    {

        var has_image = false;
        var img = '';
        var title = $( this ).parent().find( '.label' ).text();
        title = ( title ? title : '' );

        var msg = $( this ).find( 'p' ).html();
        if( msg )
        {
            var arrayOfStrings = msg.split('@');
            msg = arrayOfStrings[0] ? arrayOfStrings[0] : 'Help text coming soon.';
            if ( arrayOfStrings[1] ) {
                has_image = true;
                img = '<img class="epub-help-image" src="' + arrayOfStrings[1] + '">';
            }
        } else {
            msg = 'Help text coming soon.';
        }

        $( '#epub-dialog-info-icon-msg' ).html('<p>' + msg + '</p><br/>' + img);

        epub_help_dialog.dialog( {
            title: title,
            width: (has_image ? 1000 : 400),
            maxHeight: (has_image ? 750 : 300),
            open: function ()
            {
                // reposition dialog after image loads
                $( "#epub-dialog-info-icon" ).find( '.epub-help-image' ).one( "load", function ()
                {
                    epub_help_dialog.dialog('option', { position: { my: "center", at: "center", of: window } } );
                    //  $(this).dialog({position: {my: "center", at: "center", of: window}});
                });

                // close dialog if user clicks outside of it
                $( '.ui-widget-overlay' ).bind( 'click', function ()
                {
                    $( "#epub-dialog-info-icon" ).dialog( 'close' )
                });
            }
        }).dialog('open');
    });


    // SAVE SETTINGS DIALOG
    //====================

    $( '#epub-ajax-in-progress' ).dialog({
        resizable: false,
        height: 70,
        width: 200,
        modal: true,
        autoOpen: false
    }).hide();


    // SEND FEEDBACK DIALOG
    //====================

    $( '#epub-ajax-in-progress-feedback' ).dialog({
        resizable: false,
        height: 70,
        width: 300,
        modal: true,
        autoOpen: false
    }).hide();
    // hide the dialog top bar
    $( ".ui-dialog-titlebar" ).hide();


    // OUR OTHER PLUGINS - PREVIEW POPUP
    //====================

    (function(){
        //Open Popup larger Image
        epub.find( '.featured_img' ).on( 'click', function(){

            epub.find( '.image_zoom' ).remove();
            var img_src = $( this ).find( 'img' ).attr( 'src' );

            $( this ).after('' +
                '<div class="image_zoom">' +
                '<img src="'+img_src+'" class="image_zoom">' +
                '<span class="close icon_close"></span>'+
                '</div>' +
                '')
        });

        //Close Plugin Preview Popup
        $( 'body' ).on( 'click', '.image_zoom .close', function(){
            $( this ).parent().remove();
        });
    })();

});