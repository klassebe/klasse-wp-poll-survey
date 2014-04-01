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
require_once(ABSPATH . 'wp-admin/includes/screen.php');

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
add_action( 'wp_ajax_kwps_save_poll', 'kwps_save_poll' );

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
    global $post;

    if('kwps_poll' === $post->post_type && 'json' === get_query_var('format')  && is_singular()){
        kwps_display_json();
        exit;
    }

    return $template;
}

function kwps_display_json(){
    global $post;

    $post_as_array = (array) $post;

    $post_as_array = kwps_get_post_with_versions($post_as_array);

    wp_send_json($post_as_array);
}

function kwps_get_post_with_versions($post_as_array) {
    var_dump(get_post_custom_keys($post_as_array['ID']));

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

    return $post_as_array;
}

function kwps_save_poll(){
    if( kwps_validate_new_poll($_POST) ) {
        echo 'validated';
    }

    kwps_save_post($_POST);

    die();
}

function kwps_validate_new_poll($post) {
    $required_fields = array(
        'post_title',
        'post_status',
        'post_type',
        '_kwps_intro',
        '_kwps_outro',
        '_kwps_question',
        '_kwps_answers',
    );

    foreach($required_fields as $field)
    if(! isset($post[$field])) {
        return false;
    } else {
        if( is_string($post[$field])){
            if( strlen($post[$field]) == 0 ) {
                return false;
            }
        }
    }
    return true;
}

function kwps_save_post($post){
    $post_id = wp_insert_post($post);
    var_dump($post_id);

    if( $post_id != 0 ){
        foreach($post as $field => $value){
            if( strpos($field, 'kwps') ) {
                echo 'trying to save field: ' . $field . "<br>";
                if( update_post_meta($post_id, $field, $value) ){
                    echo 'saved ' . $field;
                } else {
                    echo 'failed ' . $field;
                }
            }
        }
    } else {
        echo 'post could not be saved';
    }
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
    wp_register_script( 'klasse-wp-poll-survey-underscore', plugins_url( 'js/bower_components/underscore/underscore.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'klasse-wp-poll-survey-backbone', plugins_url( 'js/bower_components/backbone/backbone.js', __FILE__ ), array( 'klasse-wp-poll-survey-underscore' ));
    wp_register_script( 'klasse-wp-poll-survey-handlebars', plugins_url( 'js/bower_components/handlebars/handlebars.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'klasse-wp-poll-survey-backbone-relational', plugins_url( 'js/bower_components/backbone-relational/backbone-relational.js', __FILE__ ), array( 'klasse-wp-poll-survey-backbone' ));

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'klasse-wp-poll-survey-handlebars');
    wp_enqueue_script( 'klasse-wp-poll-survey-backbone-relational');
    //wp_enqueue_script( 'klasse-wp-poll-survey-handlebars', plugins_url( 'js/bower_components/handlebars/handlebars.js', __FILE__ ), array( 'jquery' ));
    //wp_enqueue_script( 'klasse-wp-poll-survey-backbone-relational', plugins_url( 'js/bower_components/backbone-relational/backbone-relational.js', __FILE__ ), array( 'backbone' ));
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
