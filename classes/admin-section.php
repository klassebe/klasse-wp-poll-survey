<?php

namespace kwps_classes;
require_once __DIR__ . '/lists/test-collections-list-table.php';
require_once __DIR__ . '/../classes/lists/versions-list-table.php';
require_once __DIR__ . '/../classes/version-handler.php';

//require_once __DIR__ . '/../classes/post-types/version.php';

require_once __DIR__ . '/lists/entries-list-table.php';
require_once __DIR__ . '/uniqueness.php';


/**
 * Class admin_section
 *
 * This class contains all functions used to display and load data for the admin part of the plugin
 *
 * @package kwps_classes
 */
class admin_section {

    /**
     * Enqueues all styles for the admin part
     */
    static function enqueue_styles_admin_addnew() {
		wp_enqueue_style('thickbox');
		wp_enqueue_style( 'klasse_wp_poll_survey_plugin_admin_styles');
	}

    /**
     * Enqueues all scripts for the admin part, including the localization of the script
     */
    static function enqueue_scripts_admin_addnew() {
		wp_register_script( 'tinymce', plugins_url( '../assets/lib/tinymce/tinymce.min.js', __FILE__ ), array( 'jquery'));
		wp_register_script( 'klasse-wp-poll-survey-admin', plugins_url( '../assets/js/kwps_admin.js', __FILE__ ), array( 'backbone', 'thickbox', 'media-upload', 'tinymce' ));

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
			'Display Intro' => __('Display Intro', 'klasse-wp-poll-survey'),
			'Intro Result' => __('Intro Result', 'klasse-wp-poll-survey'),
			'Add Intro Result' => __('Add Intro Result', 'klasse-wp-poll-survey'),
			'Question pages' => __('Question pages', 'klasse-wp-poll-survey'),
			'question page' => __('question page', 'klasse-wp-poll-survey'),
			'Add question page' => __('Add question page', 'klasse-wp-poll-survey'),
			'Questions' => __('Questions', 'klasse-wp-poll-survey'),
			'question' => __('question', 'klasse-wp-poll-survey'),
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
			'version' => __('version', 'klasse-wp-poll-survey'),
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
			'Select the type of test you want to create.' => __('Select the type of test you want to create.', 'klasse-wp-poll-survey'),
			'Copy of' => __('Copy of', 'klasse-wp-poll-survey'),
			'Result profile' => __('Result profile', 'klasse-wp-poll-survey'),
			'You must add a result to the text' => __('You must add a result to the text', 'klasse-wp-poll-survey'),
			'This introduction is shown when someone fills out the test for the first time.' => __('This introduction is shown when someone fills out the test for the first time.', 'klasse-wp-poll-survey'),
			'For people who have already completed the test.' => __('For people who have already completed the test.', 'klasse-wp-poll-survey'),
			'Ready to Publish!' => __('Ready to Publish!'),
			'Errors' => __('Errors'),
			'Errors occurred. Please check below for more information.' => __('Errors occurred. Please check below for more information.'),
			'Collection Outro' => __('Collection Outro')
		);
		wp_localize_script( 'klasse-wp-poll-survey-admin', 'kwps_translations', $translation_array );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'klasse-wp-poll-survey-admin');

	}

    public static function enqueue_scripts() {
        if( isset( $_REQUEST['section']) && 'edit_version' == $_REQUEST['section'] ) {
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-core' );

            wp_register_script('klasse_wp_poll_survey_plugin_admin_ays', plugins_url('../js/bower_components/jquery.are-you-sure/jquery.are-you-sure.js', __FILE__));
            wp_register_script('klasse_wp_poll_survey_plugin_admin_scripts', plugins_url('../js/version-handling.js', __FILE__));
            wp_localize_script('klasse_wp_poll_survey_plugin_admin_scripts', 'WPURLS', array( 'siteurl' => get_option('siteurl') ));

            wp_enqueue_script('klasse_wp_poll_survey_plugin_admin_ays');
            wp_enqueue_script('klasse_wp_poll_survey_plugin_admin_scripts');
        } elseif( isset( $_REQUEST['section'] ) && isset( $_REQUEST['tab'] ) && 'edit_test_collection' == $_REQUEST['section'] && 'results' == $_REQUEST['tab'] ) {
            wp_register_script('klasse_wp_poll_survey_plugin_admin_results_scripts', plugins_url('../js/admin-results.js', __FILE__));
            wp_enqueue_script( 'klasse_wp_poll_survey_plugin_admin_results_scripts');
        }
    }

    /**
     * Collects all data and adds it via javascript variables wrapped in <script> tags to the view to add/edit tests
     *
     */
    public static function display_form()
    {
        if( isset( $_REQUEST['section'] ) ) {
            if( 'edit_test_collection' == $_REQUEST['section'] ) {
                $test_collection_publish_errors = Test_Collection::validate_for_publish( array('ID' => $_REQUEST['id'] ) );

                if( isset( $_REQUEST['action'] ) && 'publish' == $_REQUEST['action'] ) {
                    if( sizeof( $test_collection_publish_errors ) == 0  ) {
                        $versions = Version::get_all_by_post_parent( $_REQUEST['id'] );
                        foreach( $versions as $version ) {
                            wp_publish_post( $version['ID']);
                        }
                    }
                }
                $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'versions';
                if( 'versions' == $active_tab ) {
                    $versions_list = new \kwps_classes\Versions_List_Table();
                    $versions_list->prepare_items();
                } elseif( 'settings' == $active_tab ) {
                    if( sizeof( $_POST ) > 0 ) {
                        // TODO add validation
                        Test_Collection::update_meta_data($_REQUEST['id'], $_POST);
                    }
                    $settings = Test_Collection::get_meta_data( $_REQUEST['id'] );

//                    var_dump($settings, $_POST);
                }

                include_once __DIR__ . '/../views/edit-test-collection.php';
            } elseif( 'edit_version' == $_REQUEST['section'] ) {
                if( sizeof( $_POST ) == 0  ) {
                    if( isset( $_REQUEST['post_parent'])) {
                        $versions = Version::get_all_by_post_parent( $_REQUEST['post_parent']);
                        if( sizeof( $versions) >= 1 ) {
                            $version_data = Version::get_with_all_children( $versions[0]['ID'], true);
                        }
                    }

                    include_once __DIR__ . '/../views/edit-version.php';
                } else {
                    $formattedData = json_decode(stripslashes($_POST['formattedData']), true);
                    if( isset( $_REQUEST['id'] ) ) {
                        // validate/update existing version
                        $form_handler = new Version_Handler();
                        $validation_result = $form_handler->validate_existing_version_form($formattedData);
//                       var_dump( $validation_result ) ;
                        if( ! $validation_result['errors'] ) {
                            $version_data = $form_handler->save_new_version_form($formattedData);
                        } else {
                            $test_modus_errors = $validation_result['test_modus_errors'];
                            $version_data = $validation_result['data'];
                        }
                    } else {
                        if(! isset( $formattedData['ID'] ) ) {
                            $form_handler = new Version_Handler();
                            $validation_result = $form_handler->validate_new_version_form($formattedData);
//                           var_dump( $validation_result ) ;
                            if( ! $validation_result['errors'] ) {
                                $version_data = $form_handler->save_new_version_form($formattedData);
                            } else {
                                $test_modus_errors = $validation_result['test_modus_errors'];
                                $version_data = $validation_result['data'];
                            }
                        } else {
                            $version_data = $formattedData;
                        }
                    }
                    include_once __DIR__ . '/../views/edit-version.php';
                }
            }
        } else {
            if( sizeof( $_POST ) == 0 ) {
                include_once __DIR__ . '/../views/add-test-collection.php';
            } else {
                $data = $_POST;
                $data['_kwps_logged_in_user_limit'] = 'free';
                $data['_kwps_logged_out_user_limit'] = 'free';
                $data['_kwps_show_grouping_form'] = 0;

                $test_collection = Test_Collection::save_post($data);
                $id = $test_collection['ID'];
                $url = get_admin_url() . '/admin.php?page=' . $_REQUEST['page'] . '&section=edit_test_collection&id=' . $id;
                wp_redirect($url);
            }
        }
    }

    /**
     * Displays a list of all tests
     */
    public static function display_tests() {
        $poll_list = new Test_Collections_List_Table();
        $poll_list->prepare_items();

        include_once __DIR__ . '/../views/poll_list.php';
    }

    /**
     * Displays a list of all entries
     */
    public static function manage_entries() {
        $entry_list = new Entries_List_Table();
        $entry_list->prepare_items();

        include_once __DIR__ . '/../views/entry_list.php';
    }
} 