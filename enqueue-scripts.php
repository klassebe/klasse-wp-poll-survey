<?php

add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );


/**
 * Register and enqueues public-facing JavaScript files.
 *
 * @since    1.0.0
 */
function enqueue_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'backbone' );

    wp_register_script('klasse-wp-poll-survey-plugin-script', plugins_url('assets/js/kwps_public.js', __FILE__));
    wp_localize_script('klasse-wp-poll-survey-plugin-script', 'WPURLS', array( 'siteurl' => get_option('siteurl') ));
    wp_enqueue_script( 'klasse-wp-poll-survey-plugin-script' );
}

/**
 * Register and enqueues admin JavaScript files.
 *
 * @since    1.0.0
 */
function enqueue_scripts_admin() {

}