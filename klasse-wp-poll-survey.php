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
require_once __DIR__ . '/includes/version.php';
require_once __DIR__ . '/includes/question.php';
require_once __DIR__ . '/includes/entry.php';
require_once __DIR__ . '/includes/intro.php';
require_once __DIR__ . '/includes/outro.php';
require_once __DIR__ . '/includes/answer_option.php';
require_once __DIR__ . '/includes/test_modus.php';
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
add_action( 'wp_enqueue_styles', 'enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

add_action('init', array('\includes\version','register_post_type'));
add_action('init', array('\includes\answer_option','register_post_type'));
add_action('init', array('\includes\question','register_post_type'));
add_action('init', array('\includes\entry','register_post_type'));
add_action('init', array('\includes\intro','register_post_type'));
add_action('init', array('\includes\outro','register_post_type'));
add_action('init', array('\includes\test_modus','register_post_type'));

function my_custom_post_status(){
    register_post_status( 'duplicate', array(
        'label'                     => _x( 'Duplicate', 'kwps_test_modus' ),
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Duplicate <span class="count">(%s)</span>', 'Duplicate <span class="count">(%s)</span>' ),
    ) );
}


function kwps_fix_post_state($states){
    global $post;

    if( 'kwps_test_modus' == $post->post_type && 'duplicate' == $post->post_status ){
//        $states[] = __('Duplicate');
        $states[] = 'Duplicate';
    }
    return $states;
}
add_filter( 'display_post_states', 'kwps_fix_post_state', 10,2);


add_action( 'init', 'my_custom_post_status' );

add_action( 'admin_notices', 'custom_error_notice' );
function custom_error_notice(){
    global $current_screen, $post;
//    var_dump($post);
    if ( $current_screen->parent_base == 'edit' && $post->post_type == 'kwps_test_modus'){
        if(strlen($post->post_title) == 0){
            echo '<div class="error"><p>Post could not be saved - Title is empty</p></div>';
        }

        if( \includes\Test_Modus::has_duplicate($post->ID, $post->post_title)){
            echo '<div class="error"><p>Post could not be saved - Duplicate already exists</p></div>';
        }
    }
}

add_filter('status_save_pre', 'validate_post');
add_filter('status_update_pre', 'validate_post');

function validate_post($status){
    global $post;

    if($post->post_type == 'kwps_test_modus'){
        if(! \includes\Test_Modus::validate_for_insert() ){
            $status = 'duplicate';
        }
    }
    return $status;
}



add_action('admin_menu', 'add_plugin_admin_menu');

add_action( 'wp_ajax_kwps_save_version', array('\includes\version','save_from_request'));
add_action( 'wp_ajax_kwps_update_version', array('\includes\version','update_from_request'));
add_action( 'wp_ajax_kwps_delete_version', array('\includes\version','delete_from_request'));

add_action( 'wp_ajax_kwps_save_question', array('\includes\question','save_from_request'));
add_action( 'wp_ajax_kwps_update_question', array('\includes\question','update_from_request'));
add_action( 'wp_ajax_kwps_delete_question', array('\includes\question','delete_from_request'));

add_action( 'wp_ajax_kwps_save_answer_option', array('\includes\answer_option','save_from_request'));
add_action( 'wp_ajax_kwps_update_answer_option', array('\includes\answer_option','update_from_request'));
add_action( 'wp_ajax_kwps_delete_answer_option', array('\includes\answer_option','delete_from_request'));

add_action( 'wp_ajax_kwps_save_intro', array('\includes\intro','save_from_request'));
add_action( 'wp_ajax_kwps_update_intro', array('\includes\intro','update_from_request'));
add_action( 'wp_ajax_kwps_delete_intro', array('\includes\intro','delete_from_request'));

add_action( 'wp_ajax_kwps_save_outro', array('\includes\outro','save_from_request'));
add_action( 'wp_ajax_kwps_update_outro', array('\includes\outro','update_from_request'));
add_action( 'wp_ajax_kwps_delete_outro', array('\includes\outro','delete_from_request'));

// nopriv prefix to make sure this function is callable for unregistered users
add_action( 'wp_ajax_nopriv_kwps_save_entry', array('\includes\entry','save_from_request'));
add_action( 'wp_ajax_kwps_save_entry', array('\includes\entry','save_from_request'));
add_action( 'wp_ajax_kwps_update_entry', array('\includes\entry','update_from_request'));
add_action( 'wp_ajax_kwps_delete_entry', array('\includes\entry','delete_from_request'));

add_filter('init', 'kwps_add_api_rewrite_rules');

register_activation_hook(__FILE__, 'kwps_activate');
register_deactivation_hook(__FILE__, 'kwps_deactivate');

// shortcode -> use feip_form_posts template in front end for vote function!
add_shortcode('kwps_version', array('\includes\version', 'shortcode') );

// add_action('shutdown', function() {
//  echo '<script>jQuery(function($){
//                     setTimeout(function() {
//                         tinymce.init({
//                             selector: "textarea"
//                         });
//                     }, 5000);
//                 }); console.log("testje")</script>';
// });

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

    if('kwps_version' === $post->post_type && 'json' === get_query_var('format')  && is_singular()){
        \includes\version::display_version_as_json();
        exit;
    }

    return $template;
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
    wp_enqueue_script( 'highcharts', plugins_url( 'assets/js/highcharts.js', __FILE__ ), array( 'jquery' ));
    wp_enqueue_script( 'highcharts-exporting', plugins_url( 'assets/js/exporting.js', __FILE__ ), array( 'jquery', 'highcharts' ));
    wp_enqueue_script( 'klasse-wp-poll-survey-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ));
}

/**
 * Register and enqueues admin JavaScript files.
 *
 * @since    1.0.0
 */
function enqueue_scripts_admin() {
    wp_register_script( 'klasse-wp-poll-survey-handlebars', plugins_url( 'js/bower_components/handlebars/handlebars.js', __FILE__ ), array( 'jquery' ));
    wp_register_script( 'klasse-wp-poll-survey-backbone-associations', plugins_url( 'js/bower_components/backbone-associations/backbone-associations.js', __FILE__ ), array( 'backbone' ));
    wp_register_script( 'klasse-wp-poll-survey-admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ));


    $translation_array = array(
        '_kwps_intro' => __( 'Intro' ),
        '_kwps_outro' => __( 'Outro' ),
        '_kwps_question' => __( 'Question' )
    );
    wp_localize_script( 'klasse-wp-poll-survey-admin', 'kwps_translations', $translation_array );

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    // wp_enqueue_script( 'tiny_mce' );
    // wp_enqueue_script( 'editorremov' );
    // wp_enqueue_script( 'editor-functions' );
    // wp_enqueue_script( 'media-upload' );
    wp_enqueue_script( 'klasse-wp-poll-survey-handlebars');
    wp_enqueue_script( 'klasse-wp-poll-survey-backbone-associations');
    wp_enqueue_script( 'klasse-wp-poll-survey-admin');
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
//  else {
    // add_action('admin_enqueue_scripts', 'enqueue_scripts_admin');
    // add_action('admin_enqueue_styles', 'enqueue_styles_admin');
// }
