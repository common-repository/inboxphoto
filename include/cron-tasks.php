<?php
/* Scheduled actions */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_filter( 'cron_schedules', 'inbox_photo_add_daily_cron_schedule' );
function inbox_photo_add_daily_cron_schedule( $schedules ) {
    $schedules['daily'] = array(
        'interval' => 86400,
        'display'  => __( 'Daily', 'inboxphoto' ),
    );
    return $schedules;
}

if ( ! wp_next_scheduled( 'inbox_photo_sync_customers_action' ) ) {
    wp_schedule_event( time(), 'daily', 'inbox_photo_sync_customers_action' );
}

add_action( 'inbox_photo_sync_customers_action', 'inbox_photo_sync_customers_function' );
function inbox_photo_sync_customers_function () {
	if ( get_option( 'inbox_photo_customer_data_import' ) AND get_option( 'inbox_photo_api_token' ) ){
		$slug = get_option( 'inbox_photo_slug' );
		$token = get_option( 'inbox_photo_api_token' );
		$customers_url = 'https://'. $slug .'.inbox.photo/api/'. $token .'/customers/';
		$customers_data = json_decode(file_get_contents($customers_url));
		if( ! (get_role( 'customer' ) ) ) {
			add_role(
				'customer',
				__( 'Customer', 'inboxphoto' ),
				array(
					'read'         => true,
					'edit_posts'   => false,
					'delete_posts' => false,
				)
			);
		}
		foreach($customers_data as $customer) {
			$customer_details = json_decode(file_get_contents($customer->details));
			$userdata = array(
				'user_login'  =>  strtolower(preg_replace('/\s+/', '', $customer->name)).'_'.$customer_details->id,
				'user_email'  =>  $customer_details->email,
				'user_pass'   =>  NULL,
				'display_name' => ucwords(strtolower($customer->name)),
				'nickname' => ucwords(strtolower($customer->name)),
				'user_registered' => $customer_details->creation_date,
				'role' => 'customer'
			);

			$user_id = wp_insert_user( $userdata ) ;
		}
	}
}

function inbox_photo_role_exists( $role ) {

	if( ! empty( $role ) ) {
		return $GLOBALS['wp_roles']->is_role( $role );
	}

	return false;
}