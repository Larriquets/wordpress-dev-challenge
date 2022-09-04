<?php

if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}

//// --------------- PLUGIN ----------------- ////

register_activation_hook( PLUGIN_NAME_PLUGIN_FILE, 'mc_plugin_activation' );
function mc_plugin_activation() {
    $args = array(
        'post_status' => 'publish',
        'post_type' => 'post',
    );
    mc_check_links_post($args);

    if( ! wp_next_scheduled( 'mc_cron_job' ) ) {
	    wp_schedule_event(
            current_time( 'timestamp' ),
            'hourly', // 'everyminute',
            'mc_cron_job',
            array( 'param_1', 'param_2' )
       );
    }
}

register_deactivation_hook( PLUGIN_NAME_PLUGIN_FILE, 'wpshout_plugin_deactivation' );
function wpshout_plugin_deactivation() {
    wp_clear_scheduled_hook( 'mc_cron_job' );
}
