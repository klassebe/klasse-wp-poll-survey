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
require_once __DIR__ . '/includes/test_collection.php';
require_once __DIR__ . '/includes/version.php';
require_once __DIR__ . '/includes/question.php';
require_once __DIR__ . '/includes/question_group.php';
require_once __DIR__ . '/includes/result_profile.php';
require_once __DIR__ . '/includes/entry.php';
require_once __DIR__ . '/includes/intro.php';
require_once __DIR__ . '/includes/intro_result.php';
require_once __DIR__ . '/includes/outro.php';
require_once __DIR__ . '/includes/answer_option.php';
require_once __DIR__ . '/includes/test_modus.php';
require_once __DIR__ . '/includes/duplicate.php';
require_once __DIR__ . '/includes/locked.php';
require_once __DIR__ . '/includes/uniqueness.php';
require_once __DIR__ . '/includes/result.php';
require_once __DIR__ . '/includes/result_profile.php';
require_once __DIR__ . '/includes/bar-chart.php';

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
add_action('init', array('\includes\question_group','register_post_type'));
add_action('init', array('\includes\entry','register_post_type'));
add_action('init', array('\includes\intro','register_post_type'));
add_action('init', array('\includes\intro_result','register_post_type'));
add_action('init', array('\includes\outro','register_post_type'));
add_action('init', array('\includes\test_modus','register_post_type'));
add_action('init', array('\includes\test_collection','register_post_type'));
add_action('init', array('\includes\result_profile','register_post_type'));

add_action( 'init', array('\includes\duplicate','register_post_status' ));
add_action( 'init', array('\includes\locked','register_post_status' ));

add_action( 'init', array('\includes\uniqueness','set_cookie' ));

add_filter( 'display_post_states', array('\includes\duplicate','display_post_status'), 10,2);



add_action( 'admin_notices', array('\includes\test_modus','admin_notices' ));

add_filter('status_save_pre', array('\includes\test_modus','set_to_duplicate_when_title_exists'));
add_filter('status_update_pre', array('\includes\test_modus','set_to_duplicate_when_title_exists'));


add_action('admin_menu', 'add_plugin_admin_menu');

add_action( 'wp_ajax_kwps_save_test_collection', array('\includes\test_collection','save_from_request'));
add_action( 'wp_ajax_kwps_update_test_collection', array('\includes\test_collection','update_from_request'));
add_action( 'wp_ajax_kwps_delete_test_collection', array('\includes\test_collection','delete_from_request'));

add_action( 'wp_ajax_kwps_save_version', array('\includes\version','save_from_request'));
add_action( 'wp_ajax_kwps_update_version', array('\includes\version','update_from_request'));
add_action( 'wp_ajax_kwps_delete_version', array('\includes\version','delete_from_request'));

add_action( 'wp_ajax_kwps_save_question_group', array('\includes\question_group','save_from_request'));
add_action( 'wp_ajax_kwps_update_question_group', array('\includes\question_group','update_from_request'));
add_action( 'wp_ajax_kwps_delete_question_group', array('\includes\question_group','delete_from_request'));

add_action( 'wp_ajax_kwps_save_result_profile', array('\includes\result_profile','save_from_request'));
add_action( 'wp_ajax_kwps_update_result_profile', array('\includes\result_profile','update_from_request'));
add_action( 'wp_ajax_kwps_delete_result_profile', array('\includes\result_profile','delete_from_request'));

add_action( 'wp_ajax_kwps_save_question', array('\includes\question','save_from_request'));
add_action( 'wp_ajax_kwps_update_question', array('\includes\question','update_from_request'));
add_action( 'wp_ajax_kwps_delete_question', array('\includes\question','delete_from_request'));

add_action( 'wp_ajax_kwps_save_answer_option', array('\includes\answer_option','save_from_request'));
add_action( 'wp_ajax_kwps_update_answer_option', array('\includes\answer_option','update_from_request'));
add_action( 'wp_ajax_kwps_delete_answer_option', array('\includes\answer_option','delete_from_request'));

add_action( 'wp_ajax_kwps_save_intro', array('\includes\intro','save_from_request'));
add_action( 'wp_ajax_kwps_update_intro', array('\includes\intro','update_from_request'));
add_action( 'wp_ajax_kwps_delete_intro', array('\includes\intro','delete_from_request'));

add_action( 'wp_ajax_kwps_save_intro_result', array('\includes\intro_result','save_from_request'));
add_action( 'wp_ajax_kwps_update_intro_result', array('\includes\intro_result','update_from_request'));
add_action( 'wp_ajax_kwps_delete_intro_result', array('\includes\intro_result','delete_from_request'));

add_action( 'wp_ajax_kwps_save_outro', array('\includes\outro','save_from_request'));
add_action( 'wp_ajax_kwps_update_outro', array('\includes\outro','update_from_request'));

// nopriv prefix to make sure this function is callable for unregistered users
add_action( 'wp_ajax_nopriv_kwps_save_entry', array('\includes\entry','save_from_request'));
add_action( 'wp_ajax_kwps_save_entry', array('\includes\entry','save_from_request'));
add_action( 'wp_ajax_kwps_delete_entries_from_version', array('\includes\entry','delete_from_version'));


add_action( 'wp_ajax_kwps_get_result_of_version', array('\includes\result','get_result_of_version_by_entry_id'));
add_action( 'wp_ajax_nopriv_kwps_get_result_of_version', array('\includes\result','get_result_of_version_by_entry_id'));

add_action( 'wp_ajax_kwps_get_result_of_test_collection',
    array('\includes\result','ajax_get_result_data_of_test_collection'));

add_action( 'wp_ajax_kwps_get_result_profile', array('\includes\result_profile','ajax_get_by_entry_id'));
add_action( 'wp_ajax_kwps_get_bar_chart_per_question',
    array('\includes\bar_chart','ajax_get_chart_per_question_by_entry_id'));



add_filter('init', 'kwps_add_api_rewrite_rules');

register_activation_hook(__FILE__, 'kwps_activate');
register_deactivation_hook(__FILE__, 'kwps_deactivate');

// shortcode -> use feip_form_posts template in front end for vote function!
add_shortcode('kwps_version', array('\includes\version', 'shortcode') );
add_shortcode('kwps_result', array('\includes\result', 'shortcode') );


function kwps_activate(){
    kwps_add_api_rewrite_rules();
    flush_rewrite_rules();
    create_default_test_modi();
}

function create_default_test_modi(){
    $kwps_poll = array(
        'post_title' => 'Poll',
        'post_name' => 'kwps-poll',
        'post_status' => 'publish',
        'post_type' => 'kwps_test_modus',
        '_kwps_max_question_groups' => 1,
        '_kwps_max_questions_per_question_group' => 1,
        '_kwps_max_answer_options_per_question' => -1,
        '_kwps_allowed_input_types' => array('input_type_1', 'input_type_2'),
        '_kwps_allowed_output_types' => array('bar-chart-per-question'),
        '_kwps_answer_options_require_value' => 0,
    );

    $kwps_personality_test = array(
        'post_title' => 'Personality test',
        'post_name' => 'kwps-personality-test',
        'post_status' => 'publish',
        'post_type' => 'kwps_test_modus',
        '_kwps_max_question_groups' => -1,
        '_kwps_max_questions_per_question_group' => -1,
        '_kwps_max_answer_options_per_question' => -1,
        '_kwps_allowed_input_types' => array('input_type_1', 'input_type_2'),
        '_kwps_allowed_output_types' => array('result-profile'),
        '_kwps_answer_options_require_value' => 1,
    );

    if( ! \includes\Test_Modus::default_test_modus_exists($kwps_poll) ){
        $error = \includes\Test_Modus::save_post($kwps_poll);
    }

    if( isset($error) && null == $error ){
        //TODO add html to report error
        var_dump($error);
    }

    if( ! \includes\Test_Modus::default_test_modus_exists($kwps_personality_test) ){
        $error = \includes\Test_Modus::save_post($kwps_personality_test);
    }

    if( isset($error) && null == $error ){
        //TODO add html to report error
        var_dump($error);
    }
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
    wp_register_script( 'klasse-wp-poll-survey-admin', plugins_url( 'js/dist/kwps_admin.js', __FILE__ ), array( 'jquery', 'backbone', 'thickbox', 'media-upload' ));

    $translation_array = array(
        '_kwps_intro' => __( 'Intro' , 'klasse-wp-poll-survey'),
        '_kwps_outro' => __( 'Outro' , 'klasse-wp-poll-survey'),
        '_kwps_question' => __( 'Question' , 'klasse-wp-poll-survey'),
        'New Test' => __( 'New Test', 'klasse-wp-poll-survey'),
	    'Builder' => __('Builder', 'klasse-wp-poll-survey'),
	    'Settings' => __('Settings', 'klasse-wp-poll-survey'),
	    'Control panel' => __('Control panel', 'klasse-wp-poll-survey'),
	    'Name' => __('Name', 'klasse-wp-poll-survey'),
	    'Create' => __('Create', 'klasse-wp-poll-survey', 'klasse-wp-poll-survey'),
	    'Edit' => __('Edit', 'klasse-wp-poll-survey'),
	    'Shortcode' => __('Shortcode', 'klasse-wp-poll-survey'),
	    'View entries' => __('View entries', 'klasse-wp-poll-survey'),
	    'Results' => __('Results', 'klasse-wp-poll-survey'),
	    'View count' => __('View count', 'klasse-wp-poll-survey'),
	    'Conversion Rate' => __('Conversion Rate', 'klasse-wp-poll-survey'),
	    'Total Participants' => __('Total Participants', 'klasse-wp-poll-survey'),
	    'Make live' => __('Make live', 'klasse-wp-poll-survey'),
	    'Add Intro' => __('Add Intro', 'klasse-wp-poll-survey'),
	    'Intro Result' => __('Intro Result', 'klasse-wp-poll-survey'),
	    'Add Intro Result' => __('Add Intro Result', 'klasse-wp-poll-survey'),
	    'Question pages' => __('Question pages', 'klasse-wp-poll-survey'),
	    'Add question page' => __('Add question page', 'klasse-wp-poll-survey'),
	    'Questions' => __('Questions', 'klasse-wp-poll-survey'),
	    'Add question' => __( 'Add question', 'klasse-wp-poll-survey'),
	    'Answers' => __('Answers', 'klasse-wp-poll-survey'),
        'Add answer' => __( 'Add answer', 'klasse-wp-poll-survey'),
	    'Add outro' => __('Add outro', 'klasse-wp-poll-survey'),
	    'Update' => __('Update', 'klasse-wp-poll-survey'),
	    'Add results' => __('Add results', 'klasse-wp-poll-survey'),
	    'Add media' => __('Add media', 'klasse-wp-poll-survey'),
	    'Value' => __('Value', 'klasse-wp-poll-survey'),
	    'Title' => __('Title', 'klasse-wp-poll-survey'),
	    'Value is required' => __('Value is required', 'klasse-wp-poll-survey'),
	    'Min value' => __('Min value', 'klasse-wp-poll-survey'),
	    'Max value' => __('Max value', 'klasse-wp-poll-survey'),
	    'Min value is required' => __('Min value is required', 'klasse-wp-poll-survey'),
	    'Max value is required' => __('Max value is required', 'klasse-wp-poll-survey'),
	    'Title is required' => __('Title is required', 'klasse-wp-poll-survey'),
	    'Name is required' => __('Name is required', 'klasse-wp-poll-survey'),
	    'Type is required' => __('Type is required', 'klasse-wp-poll-survey'),
	    'Clear entries' => __('Clear entries', 'klasse-wp-poll-survey'),
	    'This will delete all entries. Are you sure?' => __('This will delete all entries. Are you sure?', 'klasse-wp-poll-survey'),
	    'Version' => __('Version', 'klasse-wp-poll-survey'),
	    'Intro' => __('Intro', 'klasse-wp-poll-survey'),
	    'Outro' => __('Outro', 'klasse-wp-poll-survey'),
	    'Intro result' => __('Intro result', 'klasse-wp-poll-survey'),
	    'Question Group' => __('Question Group', 'klasse-wp-poll-survey'),
	    'Result Profile' => __('Result Profile', 'klasse-wp-poll-survey'),
	    'Result Profiles' => __('Result Profiles', 'klasse-wp-poll-survey'),
	    'Question' => __('Question', 'klasse-wp-poll-survey'),
	    'Answer Option' => __('Answer Option', 'klasse-wp-poll-survey'),
	    'Personality test' => __('Personality test', 'klasse-wp-poll-survey'),
	    'Poll' => __('Poll', 'klasse-wp-poll-survey'),
	    'Next' => __('Next', 'klasse-wp-poll-survey'),
	    'Logged in user' => __('Logged in user', 'klasse-wp-poll-survey'),
	    'Logged out user' => __('Logged out user', 'klasse-wp-poll-survey'),
	    'Free' => __('Free', 'klasse-wp-poll-survey'),
	    'Once, based on cookie' => __('Once, based on cookie', 'klasse-wp-poll-survey'),
	    'Once, based on IP' => __('Once, based on IP', 'klasse-wp-poll-survey'),
	    'Once, based login' => __('Once, based login', 'klasse-wp-poll-survey'),
	    'Limit entries' => __('Limit entries', 'klasse-wp-poll-survey'),
	    'Add result profile' => __('Add result profile', 'klasse-wp-poll-survey'),
	    'Create new test' => __('Create new test', 'klasse-wp-poll-survey'),
	    'Test Title' => __('Test Title', 'klasse-wp-poll-survey'),
	    'This will be the title of your test.' => __('This will be the title of your test.', 'klasse-wp-poll-survey'),
	    'Test modus' => __('Test modus', 'klasse-wp-poll-survey'),
	    'Test modi' => __('Test modi', 'klasse-wp-poll-survey'),
	    'Select the type of test you want to create.' => __('Select the type of test you want to create.', 'klasse-wp-poll-survey')
	);
    wp_localize_script( 'klasse-wp-poll-survey-admin', 'kwps_translations', $translation_array );

    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-ui-core' );
    wp_enqueue_script( 'jquery-ui-tabs' );
    wp_enqueue_script( 'klasse-wp-poll-survey-admin');
}

function enqueue_styles_admin() {
    wp_enqueue_style('thickbox');
    wp_enqueue_style('editor');
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
    add_submenu_page( 'klasse-wp-poll-survey' . '_tests', __( 'Add new test', 'klasse-wp-poll-survey' ), __( 'Add new', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_addnew', array('\includes\admin_section', 'display_form'));
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
