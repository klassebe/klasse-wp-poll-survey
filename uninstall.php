<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Klasse_WP_Poll_Survey
 * @author    Toon Van de Putte <toon@klasse.be>
 * @license   GPL-2.0+
 * @link      http://klasse.be
 * @copyright 2014 Klasse
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// @TODO: Define uninstall functionality here
    $plugin_post_types = array(
        'kwps_answer_option',
        'kwps_entry',
        'kwps_intro',
        'kwps_intro_result',
        'kwps_outro',
        'kwps_question',
        'kwps_question_group',
        'kwps_result_profile',
        'kwps_test_collection',
        'kwps_test_modus',
        'kwps_version',
    );

    foreach($plugin_post_types as $post_type){
        $posts = new WP_Query( 'post_type='.$post_type );;

        foreach($posts->get_posts() as $post){
            wp_delete_post($post->ID);
        }
    }