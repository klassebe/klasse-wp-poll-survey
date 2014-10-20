<?php

add_action('admin_menu', 'add_plugin_admin_menu');

/**
 * Register the administration menu for this plugin into the WordPress Dashboard menu.
 *
 * @since    1.0.0
 */
function add_plugin_admin_menu() {

    add_menu_page(__( 'Tests', 'klasse-wp-poll-survey' ), __( 'Poll & Survey', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_tests', array('\kwps_classes\admin_section', 'display_tests'));
    $kwps_page_addnew = add_submenu_page( 'klasse-wp-poll-survey' . '_tests', __( 'Add new test', 'klasse-wp-poll-survey' ), __( 'Add new', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_addnew', array('\kwps_classes\admin_section', 'display_form'));
    add_submenu_page( 'klasse-wp-poll-survey' . '_tests', __( 'Manage entries', 'klasse-wp-poll-survey' ), __( 'Entries', 'klasse-wp-poll-survey' ), "edit_posts", 'klasse-wp-poll-survey' . '_manage_entries', array('\kwps_classes\admin_section', 'manage_entries'));
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