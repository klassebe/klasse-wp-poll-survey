<?php
/**
 * Klasse WordPress Poll & Survey.
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
 * Version:           0.9.0
 * Author:            Toon - Klasse
 * Author URI:        http://klasse.be
 * Text Domain:       klasse-wp-poll-survey
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/klassebe/klasse-wp-poll-survey
 * GitHub Branch:     master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once __DIR__ . '/includes/admin-section.php';
require_once __DIR__ . '/includes/post-types/test-collection.php';
require_once __DIR__ . '/includes/post-types/version.php';
require_once __DIR__ . '/includes/post-types/question.php';
require_once __DIR__ . '/includes/post-types/question_group.php';
require_once __DIR__ . '/includes/post-types/result-profile.php';
require_once __DIR__ . '/includes/post-types/entry.php';
require_once __DIR__ . '/includes/post-types/intro.php';
require_once __DIR__ . '/includes/post-types/intro-result.php';
require_once __DIR__ . '/includes/post-types/outro.php';
require_once __DIR__ . '/includes/post-types/answer-option.php';
require_once __DIR__ . '/includes/post-types/test-modus.php';
require_once __DIR__ . '/includes/post-types/coll-outro.php';
require_once __DIR__ . '/includes/post-statuses/duplicate.php';
require_once __DIR__ . '/includes/post-statuses/locked.php';
require_once __DIR__ . '/includes/uniqueness.php';
require_once __DIR__ . '/includes/result.php';
require_once __DIR__ . '/includes/post-types/result-profile.php';
require_once __DIR__ . '/includes/charts/bar-chart.php';
require_once __DIR__ . '/includes/charts/pie-chart.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/post-types/result-group.php';
require_once __DIR__ . '/includes/overlay.php';

require_once(ABSPATH . 'wp-admin/includes/screen.php');


// Load public-facing style sheet and JavaScript.
add_action( 'wp_enqueue_styles', 'enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

include_once 'register-post-types.php';

include_once 'register-post-statuses.php';

add_action( 'init', array('\includes\uniqueness','set_cookie' ));

add_action('init', array( '\includes\session', 'myStartSession' ), 1  );
add_action('wp_logout', array( '\includes\session', 'myEndSession' ) );
add_action('wp_login', array( '\includes\session', 'myEndSession' ) );

add_filter( 'display_post_states', array('\includes\duplicate','display_post_status'), 10,2);


add_action( 'admin_notices', array('\includes\test_modus','admin_notices' ));

add_filter('status_save_pre', array('\includes\test_modus','set_to_duplicate_when_title_exists'));
add_filter('status_update_pre', array('\includes\test_modus','set_to_duplicate_when_title_exists'));


add_action('admin_menu', 'add_plugin_admin_menu');

include_once 'ajax-calls.php';



add_filter('init', 'kwps_add_api_rewrite_rules');

register_activation_hook(__FILE__, 'kwps_activate');
register_deactivation_hook(__FILE__, 'kwps_deactivate');

// shortcode -> use feip_form_posts template in front end for vote function!
add_shortcode('kwps_version', array('\includes\version', 'shortcode') );
add_shortcode('kwps_test_collection', array('\includes\test_collection', 'shortcode') );
add_shortcode('kwps_result', array('\includes\result', 'shortcode') );


function kwps_activate(){
    kwps_add_api_rewrite_rules();
    flush_rewrite_rules();
    \includes\Test_Modus::create_default_test_modi();
}



function kwps_deactivate(){
    flush_rewrite_rules();
}

function kwps_add_api_rewrite_rules(){
    add_rewrite_endpoint('format', EP_PERMALINK);
}

add_filter('template_include', 'kwps_template_include', 99);

function kwps_template_include($template){
//    global $post;
//
//    if('kwps_version' === $post->post_type && 'json' === get_query_var('format')  && is_singular()){
//        \includes\version::display_version_as_json();
//        exit;
//    }

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
	wp_enqueue_script( 'backbone' );
    wp_enqueue_script( 'klasse-wp-poll-survey-plugin-script', plugins_url( 'assets/js/kwps_public.js', __FILE__ ), array( 'jquery' ));
}

/**
 * Register and enqueues admin JavaScript files.
 *
 * @since    1.0.0
 */
function enqueue_scripts_admin() {

}



function enqueue_styles_admin() {
}


/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */
function add_plugin_admin_menu() {

    add_menu_page(__( 'Tests', 'klasse-wp-poll-survey' ), __( 'Poll & Survey', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_tests', array('\includes\admin_section', 'display_tests'));
	$kwps_page_addnew = add_submenu_page( 'klasse-wp-poll-survey' . '_tests', __( 'Add new test', 'klasse-wp-poll-survey' ), __( 'Add new', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_addnew', array('\includes\admin_section', 'display_form'));
    add_submenu_page( 'klasse-wp-poll-survey' . '_tests', __( 'Manage entries', 'klasse-wp-poll-survey' ), __( 'Entries', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_manage_entries', array('\includes\admin_section', 'manage_entries'));
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
	load_plugin_textdomain('klasse-wp-poll-survey', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
//  else {
    // add_action('admin_enqueue_scripts', 'enqueue_scripts_admin');
    // add_action('admin_enqueue_styles', 'enqueue_styles_admin');
// }
