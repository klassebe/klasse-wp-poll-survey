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

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-name.php` with the name of the plugin's class file
 *
 */
require_once( plugin_dir_path( __FILE__ ) . 'public/class-klasse-wp-poll-survey.php' );
require 'vendor/autoload.php';

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */


// Load public-facing style sheet and JavaScript.
add_action( 'wp_enqueue_scripts', 'enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

add_action('init', 'kwps_register_post_types');
add_action('add_meta_boxes', 'kwps_add_metaboxes');

add_action('admin_menu', 'add_plugin_admin_menu');

function kwps_register_post_types(){
    $poll_args = array(
        'public' => true,
        'rewrite' => array(
            'slug' => 'polls',
            'with_front' => false,
        ),
        'supports' => array(
            'title',
        ),
        'labels' => array(
            'name' => 'Polls',
            'singular_name' => 'Poll',
            'add_new' => 'Add New Poll',
            'add_new_item' => 'Add New Poll',
            'edit_item' => 'Edit Poll',
            'new_item' => 'New Poll',
            'view_item' => 'View Poll',
            'search_items' => 'Search Polls',
            'not_found' => 'No Polls Found',
            'not_found_in_trash' => 'No Polls Found In Trash',
        ),
//        'show_in_menu' => false,
        'show_in_menu' => 'klasse-wp-poll-survey_tests',
    );

    register_post_type('kwps_poll', $poll_args);

}

function klwps_add_metaboxes() {
    add_meta_box('klwps_intro_and_outro', 'Intro en Outro', 'klwps_display_intro_and_outro_metabox', 'klwps_poll', 'normal', 'high');
    add_meta_box('klwps_questions', 'Question', 'klwps_display_questions_metabox', 'klwps_poll', 'normal', 'high');
}

function klwps_display_intro_and_outro_metabox($post) {
    wp_nonce_field( basename( __FILE__ ), 'klwps_nonce' );

    $intro = get_post_meta($post->ID, '_klwps_intro', true);
    $outro = get_post_meta($post->ID, '_klwps_outro', true);

    ?>
    <label for="_klwps_intro">Intro</label>
    <textarea name="_klwps_intro"><?php echo $intro; ?></textarea>

    <label for="_klwps_outro">Outro</label>
    <textarea name="_klwps_outro"><?php echo $outro; ?></textarea>

<?php
}

function klwps_display_questions_metabox($post){
    $saved_custom_fields = get_post_custom_keys($post->ID);

    $answer_options = array();

    foreach($saved_custom_fields as $custom_field){
        if (strpos($custom_field, '_klwps_answer_') !== false) {
            array_push($answer_options, $custom_field);
        }
    }
    ?>

    <label for="_klwps_question">Question</label>
    <textarea name="_klwps_question"><?php echo get_post_meta($post->ID, '_klwps_question', true); ?></textarea>

    <?php
    if(count($answer_options) < 2 ){
        ?>
        <label for="_klwps_answer_1">Answer 1</label>
        <textarea name="_klwps_answer_1"><?php echo get_post_meta($post->ID, '_klwps_answer_1', true); ?></textarea>

        <label for="_klwps_answer_2">Answer 2</label>
        <textarea name="_klwps_answer_2"><?php echo get_post_meta($post->ID, '_klwps_answer_2', true); ?></textarea>
    <?php
    }
    foreach($answer_options as $key){
        $label = substr(strrchr($key, "_"), 1);
    ?>
        <label for="<?php echo $key; ?>">Answer <?php echo $label;?></label>
        <textarea name="<?php echo $key; ?>"><?php echo get_post_meta($post->ID, $key, true); ?></textarea>
    <?php
    }
}

/**
 * Saves the custom meta input
 */
function kwps_meta_save( $post_id ) {

    $allowdHtmlTags = array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br' => array(),
        'em' => array(),
        'strong' => array(),
        'h1' => array(),
        'p' => array(),
    );

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'kwps_nonce' ] ) && wp_verify_nonce( $_POST[ 'kwps_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ '_kwps_intro' ] ) ) {
        update_post_meta( $post_id, '_kwps_intro', wp_kses( $_POST[ '_kwps_intro' ], $allowdHtmlTags ) );
    }

    if( isset( $_POST[ '_kwps_outro' ] ) ) {
        update_post_meta( $post_id, '_kwps_outro', wp_kses( $_POST[ '_kwps_outro' ], $allowdHtmlTags ) );
    }

    if( isset( $_POST[ '_klwps_question' ] ) ) {
        update_post_meta( $post_id, '_klwps_question', wp_kses( $_POST[ '_klwps_question' ], $allowdHtmlTags ) );
    }

    $field_prefix = '_klwps_answer_';
    $saved_custom_fields = get_post_custom_keys($post_id);
    $form_fields = array_keys($_POST);

    foreach($saved_custom_fields as $custom_field){
        if(! in_array($custom_field, $form_fields)){
            delete_post_meta($post_id, $custom_field);
        }
    }

    foreach($form_fields as $form_field){
        if (strpos($form_field, $field_prefix) !== false) {
            update_post_meta( $post_id, $form_field, wp_kses( $_POST[ $form_field ], $allowdHtmlTags ) );
        }
    }





}
add_action( 'save_post', 'kwps_meta_save' );

/**
 * Register and enqueue public-facing style sheet.
 *
 * @since    1.0.0
 */
function enqueue_styles() {
    wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
}

/**
 * Register and enqueues public-facing JavaScript files.
 *
 * @since    1.0.0
 */
function enqueue_scripts() {
    wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
}


/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */
function add_plugin_admin_menu() {

    add_menu_page(__( 'Tests', 'klasse-wp-poll-survey' ), __( 'Poll & Survey', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_tests', array( $this, 'display_tests' ));
    //add_submenu_page( $this->plugin_slug . '_tests', __( 'Tests', $this->plugin_slug ), __( 'Tests', $this->plugin_slug ), "edit_posts", $this->plugin_slug . '_tests2', 'display_plugin_admin_page');
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

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-klasse-wp-poll-survey-admin.php' );
	add_action( 'plugins_loaded', array( 'Klasse_WP_Poll_Survey_Admin', 'get_instance' ) );

}
