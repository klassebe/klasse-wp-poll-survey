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
require_once __DIR__ . '/includes/kwps-plugin.php';
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


include_once 'register-post-types.php';
include_once 'register-post-statuses.php';


add_action('init', 'debug_settings');
function debug_settings(){
    ini_set('xdebug.var_display_max_depth', 10);
    ini_set('xdebug.var_display_max_children', 256);
    ini_set('xdebug.var_display_max_data', 1024);
}

include_once 'add-session.php';

add_filter( 'display_post_states', array('\includes\duplicate','display_post_status'), 10,2);
add_action( 'admin_notices', array('\includes\test_modus','admin_notices' ));

add_filter('status_save_pre', array('\includes\test_modus','set_to_duplicate_when_title_exists'));
add_filter('status_update_pre', array('\includes\test_modus','set_to_duplicate_when_title_exists'));

include_once 'add-ajax-calls.php';

register_activation_hook(__FILE__, array( '\includes\kwps_plugin' ,'on_activate' ) );
register_deactivation_hook(__FILE__, array( '\includes\kwps_plugin' ,'on_deactivate' ) );

include_once 'add-shortcodes.php';

include_once 'enqueue-scripts.php';
include_once 'enqueue-styles.php';
include_once 'add-admin-menu.php';