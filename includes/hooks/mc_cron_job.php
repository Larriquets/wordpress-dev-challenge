<?php

if ( ! defined('ABSPATH') ) {
    die('Direct access not permitted.');
}


//// --------------- CRON JOB ------------------ ////

add_filter( 'cron_schedules', 'mc_add_cron_interval' );
function mc_add_cron_interval( $schedules ) {
    $schedules['everyminute'] = array(
            'interval'  => 60, // time in seconds
            'display'   => 'Every Minute'
    );
    return $schedules;
}

add_action( 'mc_cron_job', 'mc_check_post_cron', 10, 2 );
function mc_check_post_cron( $param1, $param2 ) {
    $args = array(
        'post_status' => 'publish',
        'post_type' => 'post',
        'meta_query' => array(
            'meta_query' => array(
                array( 
                    'key' => 'id_cron_post_checked',
                    'value' => 'unchecked',
                    'compare' => '='
                ) 
            )
        )
    );
    mc_check_links_post( $args);
}