<?php

/**
 * Klasse WP Poll Survey.
 *
 * @package   Klasse_WP_Poll_Survey
 * @author    Koen - AppSaloon <koen@appsaloon.be>
 * @license   GPL-2.0+
 * @link      @TODO http://example.com
 * @copyright 2014 Klasse
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-plugin-name-admin.php`
 *
 * @package Klasse_WP_Poll_Survey
 * @author  Koen - AppSaloon <koen@appsaloon.be>
 */
class Klasse_WP_Poll_Survey {

	/**
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'klasse-wp-poll-survey';

    private static $table_prefix = 'kwps_';
    public static $tables = array(
        'test',
        'mode',
        'version',
        'question',
        'response_option',
        'entry',
        'status',
        'tro'
    );

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide = false ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide = false ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {

        global $wpdb;

        $fullPrefix = $wpdb->prefix . self::$table_prefix;

        $query = array(
            //Status
            "CREATE TABLE `{$fullPrefix}status`  (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `label` varchar(50) NOT NULL,
                `entity` varchar(50) NOT NULL,
                PRIMARY KEY (`id`)
            );",
            //Test
            "CREATE TABLE `{$fullPrefix}test`  (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `description` TEXT NOT NULL,
                `view_count` mediumint(9) NOT NULL DEFAULT 0,
                `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` TIMESTAMP,
                `publish_date` TIMESTAMP,
                `close_date` TIMESTAMP,
                `user_id` mediumint(9) NOT NULL,
                `mode_id` mediumint(9),
                `status_id` mediumint(9),
                PRIMARY KEY (`id`)
            );",
            //Mode
            "CREATE TABLE `{$fullPrefix}mode`  (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `description` TEXT NOT NULL,
                `status_id` mediumint(9),
                PRIMARY KEY (`id`)
            );",
            //Version
            "CREATE TABLE `{$fullPrefix}version`  (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `name` varchar(50) NOT NULL,
                `api_key` varchar(50) NOT NULL,
                `test_id` mediumint(9),
                `intro_id` mediumint(9),
                `outro_id` mediumint(9),
                `status_id` mediumint(9),
                PRIMARY KEY (`id`)
            );",
            //Question
            "CREATE TABLE `{$fullPrefix}question`  (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `version_id` mediumint(9),
                `order` mediumint(9),
                `description` TEXT NOT NULL,
                `status_id` mediumint(9),
                PRIMARY KEY (`id`)
            );",
            //Response Option
            // field: order: Usefull? We have to randomize the order I think
            "CREATE TABLE `{$fullPrefix}response_option`  (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `question_id` mediumint(9),
                `description` TEXT NOT NULL,
                `value` varchar(255) NOT NULL,
                `order` mediumint(9),
                `status_id` mediumint(9),
                PRIMARY KEY (`id`)
            );",
            //Entry
            "CREATE TABLE `{$fullPrefix}entry`  (
                `session_id` varchar(255) NOT NULL,
                `response_option_id` mediumint(9),
                `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `update_date` TIMESTAMP,
                PRIMARY KEY (`session_id`, `response_option_id`)
            );",
            //Tro
            "CREATE TABLE `{$fullPrefix}tro`  (
                `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                `html_text` TEXT,
                PRIMARY KEY (`id`)
            );",
        );

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($query);

        //sleep(20);
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
        //@TODO Deactivate plugin;
	}

	/**
	 * Fired for each blog when the plugin is uninstalled.
	 *
	 * @since    1.0.0
	 */
	public static function uninstall() {
        global $wpdb;

        $tableDefaultPrefix = $wpdb->prefix . self::$table_prefix;

        $query = "SHOW TABLES LIKE '{$tableDefaultPrefix}%'";
        $tables = $wpdb->get_results($query, ARRAY_N);

        foreach($tables as $table) {
            $wpdb->query("DROP TABLE {$table[0]}");
        }
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

}
