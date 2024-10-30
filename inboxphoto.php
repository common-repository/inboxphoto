<?php
/*
Plugin Name: inbox.photo helper
Plugin URI: https://www.koffeeware.com/en/2016/12/12/wordpress-plug-in-for-inbox-photo/
Description: Streamline integration of inbox.photo.
Tags: photo, photobook, print, calendar, card, mug, t-shirt, canvas, collage, poster, w2p, web-to-print, web-to-store
Version: 2.4.3
Author: Koffeeware
Author URI: https://www.koffeeware.com
Developer: Carl Conrad
Developer URI: https://www.carlconrad.net/
License: GPL2
Text Domain: inboxphoto
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once( 'include/settings.php' );
require_once( 'include/actions.php' );
require_once( 'include/widget.php' );
require_once( 'include/cron-tasks.php' );

if ( is_admin() ){
	add_action( 'admin_menu' , 'inbox_photo_options_page' );
	add_action( 'admin_init' , 'register_inbox_photo_settings' );
//	add_action( 'admin_init' , 'inboxphoto_scripts' );
}
else {
	add_action( 'wp_head' , 'inbox_photo_prefetch' );
	add_action( 'wp_head' , 'inbox_photo_hook_css' );
	add_shortcode( 'inboxphoto' , 'shortcode_inbox_photo_button_func' );
	add_shortcode( 'inboxphoto_button' , 'shortcode_inbox_photo_button_func' );
	add_shortcode( 'inboxphoto_snippet' , 'shortcode_inbox_photo_snippet_func' );
	add_shortcode( 'inboxphoto_iframe' , 'shortcode_inbox_photo_iframe_func' );
}

add_action('plugins_loaded', 'inboxphoto_load_textdomain');
function inboxphoto_load_textdomain() {
	load_plugin_textdomain( 'inboxphoto', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

add_filter( 'plugin_action_links', 'inboxphoto_plugin_add_settings_link', 10, 5 );
function inboxphoto_plugin_add_settings_link( $actions, $plugin_file ) {
	static $plugin;

	if (!isset($plugin))
		$plugin = plugin_basename(__FILE__);
	if ($plugin == $plugin_file) {
		$settings = array('settings' => '<a href="'.admin_url( 'options-general.php?page=inbox_photo' ).'">'. __( 'Settings' ) .'</a>');
		$actions = array_merge($settings, $actions);
	}
	return $actions;
}
