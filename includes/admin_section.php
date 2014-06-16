<?php

namespace includes;
require_once __DIR__ . '/testCollections_list_table.php';
require_once __DIR__ . '/entries_list_table.php';
require_once __DIR__ . '/uniqueness.php';


/**
 * Class admin_section
 *
 * This class contains all functions used to display and load data for the admin part of the plugin
 *
 * @package includes
 */
class admin_section {


    /**
     * Enqueues all styles for the admin part
     */
    static function enqueue_styles_admin_addnew() {
		wp_enqueue_style('thickbox');
		wp_enqueue_style( 'klasse-wp-poll-survey-plugin-admin-styles', plugins_url( '../assets/css/kwps_admin.css', __FILE__ ));
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
			'Errors occurred. Please check below for more information.' => __('Errors occurred. Please check below for more information.')
		);
		wp_localize_script( 'klasse-wp-poll-survey-admin', 'kwps_translations', $translation_array );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'klasse-wp-poll-survey-admin');

	}

    /**
     * Collects all data and adds it via javascript variables wrapped in <script> tags to the view to add/edit tests
     *
     */
    public static function display_form()
    {
		self::enqueue_styles_admin_addnew();
		self::enqueue_scripts_admin_addnew();

	    $kwps_uniqueness_options = array(
            'logged_in' => Uniqueness::get_options_for_logged_in_users(),
            'logged_out' => Uniqueness::get_options_for_logged_out_users(),
        );

        $kwps_test_modi = Test_Modus::get_published_modi();

        if( isset($_GET['action']) && 'edit' === $_GET['action']){

            if( isset($_GET['id']) ) {
                $current_post = get_post($_GET['id']);

                if( null === $current_post ) {
                    echo 'post not found';
                } elseif ( 'kwps_test_collection' !== $current_post->post_type ) {
                    echo 'post not of type kwps_test_collection';
                } else {
                    $test_collection = Test_Collection::get_as_array($current_post->ID);

                    $tests = array($test_collection);

                    $versions = Version::get_all_by_post_parent($current_post->ID);

                    $question_groups = array();
                    $result_profiles = array();
                    $questions = array();
                    $intros = array();
                    $intro_results = array();
                    $outros = array();
                    $answer_options = array();

                    foreach($versions as $version){
                        $question_groups = array_merge($question_groups, Question_Group::get_all_by_post_parent($version['ID']));
	                    $result_profiles = array_merge($result_profiles, Result_Profile::get_all_by_post_parent($version['ID']));
                        $intros = array_merge($intros, Intro::get_all_by_post_parent($version['ID']));
	                    $intro_results = array_merge($intro_results, Intro_Result::get_all_by_post_parent($version['ID']));
                        $outros = array_merge($outros, Outro::get_all_by_post_parent($version['ID']));
                    }

                    foreach($versions as $key => $value){
                        $versions[$key]['total_participants'] =
                            Result::get_participants_count_of_version($value['ID']);
                        $versions[$key]['conversion_rate'] = Result::get_conversion_rate_of_version($value);
                    }

                    foreach($question_groups as $question_group){
                        $questions = array_merge($questions, Question::get_all_by_post_parent($question_group['ID']));
                    }

                    foreach($questions as $question){
                        $answer_options = array_merge($answer_options, Answer_Option::get_all_by_post_parent($question['ID']));
                    }

                    $tests = array_merge(
                        $tests, $versions, $question_groups, $result_profiles, $questions, $intros, $intro_results,
                        $outros, $answer_options, $kwps_test_modi
                    );
                ?>
                    <script>var kwpsTests=<?php echo json_encode($tests); ?></script>
                <?php
                }
                ?>
                <script>var kwpsUniquenessTypes=<?php echo json_encode(Uniqueness::get_types()) ?></script>
                <?php
                } else {
                echo 'No post id given!';
            }
        } else {

            ?>
            <script>var kwpsTests=<?php echo json_encode($kwps_test_modi); ?></script>
            <?php
        }

            ?>

        <script>var kwpsUniquenessTypes=<?php echo json_encode($kwps_uniqueness_options) ?></script>
        <script>var kwpsTestModi=<?php echo json_encode(Test_Modus::get_published_modi()) ?></script>
        <?php

        include_once __DIR__ . '/../views/add.php';

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