<?php

if ( ! defined('WP_UNINSTALL_PLUGIN' ) ) {
    die;
}
 
unregister_setting( 'inbox-photo', 'inbox_photo_slug' );
unregister_setting( 'inbox-photo', 'inbox_photo_api_token' );
unregister_setting( 'inbox-photo', 'inbox_photo_button_css' );
unregister_setting( 'inbox-photo', 'inbox_photo_button_text' );
unregister_setting( 'inbox-photo', 'inbox_photo_currency' );unregister_setting( 'inbox-photo', 'inbox_photo_link_google_analytics' );unregister_setting( 'inbox-photo', 'inbox_photo_link_google_tag_manager' );