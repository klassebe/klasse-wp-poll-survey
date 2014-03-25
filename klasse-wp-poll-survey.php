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
            'editor',
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

function kwps_add_metaboxes() {
    add_meta_box('kwps_intro_and_outro', 'Intro en Outro', 'kwps_display_intro_and_outro_metabox', 'kwps_poll', 'normal', 'high');
}

function kwps_display_intro_and_outro_metabox($post) {
    $intro = get_post_meta($post->ID, '_kwps_intro', true);
    ?>
    <label for="kwps_intro">Intro</label>
    <input type="text" name="kwps_intro" value="<?php echo $intro?>" />
<?php
}

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


}



