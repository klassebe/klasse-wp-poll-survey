<?php
/**
 * Klasse Poll & Survey.
 *
 * Embed polls & surveys on your website
 *
 * @package   Klasse_WP_Poll_Survey
 * @author    Toon Van de Putte <toon@klasse.be>
 * @license   GPL-2.0+
 * @link      http://klasse.be
 * @copyright 2014 Klasse
 *
 * @wordpress-plugin
 * Plugin Name:       Klasse WordPress Poll & Survey
 * Plugin URI:        @TODO
 * Description:       Embed polls & surveys on your website
 * Version:           1.0.0
 * Author:            Toon - Klasse
 * Author URI:        http://klasse.be
 * Text Domain:       @TODO
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/klassebe/klasse-wp-poll-survey
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/includes/admin_section.php';
require_once __DIR__ . '/includes/poll.php';

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name.php` with the name of the plugin's class file
 *
 */
require 'vendor/autoload.php';

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */


// Load public-facing style sheet and JavaScript.
add_action( 'wp_enqueue_scripts', 'enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

add_action('init', array('\includes\poll','kwps_register_post_types'));
add_action('add_meta_boxes', array('\includes\poll', 'kwps_add_metaboxes'));

add_action('admin_menu', 'add_plugin_admin_menu');

add_action( 'save_post', array('\includes\poll', 'kwps_meta_save'));

add_filter('init', 'kwps_add_api_rewrite_rules');

register_activation_hook(__FILE__, 'kwps_activate');
register_deactivation_hook(__FILE__, 'kwps_deactivate');

function kwps_activate(){
    kwps_add_api_rewrite_rules();
    flush_rewrite_rules();
}

function kwps_deactivate(){
    flush_rewrite_rules();
}

function kwps_add_api_rewrite_rules(){
    add_rewrite_endpoint('format', EP_PERMALINK);
}

add_filter('template_include', 'kwps_template_include', 99);

function kwps_template_include($template){
    if(get_query_var('format') == 'json' && is_singular()){
        kwps_display_json();
        exit;
    }

    return $template;
}

function kwps_display_json(){
    global $post;

    $post_as_array = (array) $post;
    foreach(get_post_custom_keys($post_as_array['ID']) as $custom_field){
        $meta_data = get_post_meta($post_as_array['ID'], $custom_field, true);
        $post_as_array[$custom_field] = $meta_data;
    }

//    retrieve children of this post aka versions
    $args = array('post_type' => 'kwps_poll', 'post_parent' => $post_as_array['ID']);
    $versions = get_posts($args);

    $versions_array = array();

    foreach($versions as $version){
        $version_as_array = (array) $version;
        foreach(get_post_custom_keys($version_as_array['ID']) as $custom_field){
            $meta_data = get_post_meta($version_as_array['ID'], $custom_field, true);
            $version_as_array[$custom_field] = $meta_data;
        }
        array_push($versions_array, $version_as_array);
    }

    $post_as_array['versions'] = $versions_array;

    wp_send_json($post_as_array);
}

/**
 * Register and enqueue public-facing style sheet.
 *
 * @since    1.0.0
 */
function enqueue_styles() {
    wp_enqueue_style( 'klasse-wp-poll-survey-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array());
}

/**
 * Register and enqueues public-facing JavaScript files.
 *
 * @since    1.0.0
 */
function enqueue_scripts() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'klasse-wp-poll-survey-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ));
}

/**
 * Register and enqueues admin JavaScript files.
 *
 * @since    1.0.0
 */
function enqueue_scripts_admin() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'klasse-wp-poll-survey-plugin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ));

}

function enqueue_styles_admin() {
    wp_enqueue_style( 'klasse-wp-poll-survey-plugin-jquery-ui-core', plugins_url( 'css/jquery-ui/jquery.ui.core.min.css', __FILE__ ));
    wp_enqueue_style( 'klasse-wp-poll-survey-plugin-jquery-ui-tabs', plugins_url( 'css/jquery-ui/jquery.ui.tabs.min.css', __FILE__ ));
    wp_enqueue_style( 'klasse-wp-poll-survey-plugin-admin-styles', plugins_url( 'css/admin.css', __FILE__ ));
}


/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */
function add_plugin_admin_menu() {

    add_menu_page(__( 'Tests', 'klasse-wp-poll-survey' ), __( 'Poll & Survey', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_tests', array('\includes\admin_section', 'display_tests'));
//    add_menu_page(__( 'Tests', 'klasse-wp-poll-survey' ), __( 'Poll & Survey', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_tests', array($this, 'display_tests'));
    add_submenu_page( 'klasse-wp-poll-survey' . '_tests', __( 'Add New Test', 'klasse-wp-poll-survey' ), __( 'Add New', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_addnew', array('\includes\admin_section', 'display_form'));
}

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

    add_action('admin_init', 'enqueue_scripts_admin');
    add_action('admin_init', 'enqueue_styles_admin');
}
