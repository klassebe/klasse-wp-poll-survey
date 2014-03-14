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
require_once( plugin_dir_path( __FILE__ ) . 'public/class-klass-wp-poll-survey.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Klasse_WP_Poll_Survey', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Klasse_WP_Poll_Survey', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Klasse_WP_Poll_Survey', 'get_instance' ) );

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
