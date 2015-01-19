<?php

class New_Personality_Test_Version_Handler_Test extends WP_UnitTestCase {

    protected $test_modus_personality_test;
    protected $test_collection;

    function setUp()
    {
        parent::setUp();

        ini_set('xdebug.var_display_max_depth', 25);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 2048);

        $this->truncate_tables();

        \kwps_classes\Test_Modus::create_default_test_modi();
        $personality_tests = get_posts( array(
            'post_type' => 'kwps_test_modus',
            'name' => 'kwps-personality-test',
            'post_status' => 'publish',
            )
        );

        $personality_test_modus_id = $personality_tests[0]->ID;
        $this->test_modus_personality_test = \kwps_classes\Test_Modus::get_as_array( $personality_test_modus_id );

        $this->test_collection = \kwps_classes\Test_Collection::save_post( array(
            'post_title' => 'Personality test',
            'post_parent' => $personality_test_modus_id,
        ) );

    }

    function tearDown()
    {
        parent::tearDown();
        $this->truncate_tables();
    }

    function truncate_tables() {
        global $wpdb;

        $wpdb->query( 'TRUNCATE ' . $wpdb->posts );
        $wpdb->query( 'TRUNCATE ' . $wpdb->postmeta );
    }


    function test_new_personality_test_version_form_validation_Empty() {
        $this->checkOutputWithFormTestData( 'personality-test/empty.php', false);
    }

    function test_new_personality_test_version_form_validation_NoIntroIndex() {
        $this->checkOutputWithFormTestData( 'personality-test/no-intro-index.php');
    }

    function test_new_personality_test_version_form_validation_no_answer_option_value() {
        $this->checkOutputWithFormTestData( 'personality-test/no-answer-option-value.php' );
    }

    function test_new_personality_test_version_form_validation_NoOutroIndex() {
        $this->checkOutputWithFormTestData( 'personality-test/no-outro-index.php');
    }

    function test_new_personality_test_version_form_validation_NoQuestionGroupsIndex() {
        $this->checkOutputWithFormTestData( 'personality-test/no-question-groups-index.php');
    }

    function test_new_personality_test_version_form_validation_NoIntroResultIndex() {
        $this->checkOutputWithFormTestData( 'personality-test/no-intro-result-index.php');
    }

    function test_new_personality_test_version_form_validation_NotAnArray() {
        $this->checkOutputWithFormTestData( 'personality-test/not-an-array.php', false);
    }

    function test_new_personality_test_version_form_validation_valid() {
        $this->checkOutputWithFormTestData( 'personality-test/valid.php');
    }

//    TODO check if this needs to be validated and how to handle this properly!

//       function test_new_personality_test_version_form_validation_QuestionWithoutAnswerOptionsIndex() {
//        $this->checkOutputWithFormTestData( 'personality-test/question-without-answer-options-index.php');
//    }

    function test_new_personality_test_version_form_validation_QuestionWithOneAnswerOption() {
        $this->checkOutputWithFormTestData( 'personality-test/question-with-one-answer-option.php');
    }

    function test_new_personality_test_version_form_validation_QuestionWithEmptyAnswerOptionArray() {
        $this->checkOutputWithFormTestData( 'personality-test/question-with-empty-answer-option-array.php');
    }

    function checkOutputWithFormTestData($file, $link_to_test_collection = true) {
        $test_data = include __DIR__ . '/../form-test-data/new-version/' . $file;
        $input = $test_data['input'];
        $expected_output = $test_data['expected_output'];

        if( $link_to_test_collection ) {
            $input['post_parent'] = $this->test_collection['ID'];
            $expected_output['data']['post_parent'] = $this->test_collection['ID'];
        }

        $version_handler = new \kwps_classes\Version_Handler();
        $output = $version_handler->validate_new_version_form( $input );

//        var_dump( $expected_output['test_modus_errors'], $output['test_modus_errors']);
//        var_dump($expected_output);
//        var_dump( $expected_output['errors'], $output['errors']);

        $this->assertEquals($output['errors'], $expected_output['errors']);
        $this->assertEquals($output['test_modus_errors'], $expected_output['test_modus_errors']);
        $this->assertTrue( $this->arrays_are_similar( $output['data'], $expected_output['data'] ) );
    }

    ////// saving a new version is tested below

    function test_save_new_version() {
        $test_data = include __DIR__ . '/../form-test-data/new-version/personality-test/valid-for-save-test.php';
        $input = $test_data['input'];
        $expected_output = $test_data['expected_output'];

        $version_handler = new \kwps_classes\Version_Handler();
        $output = $version_handler->save_new_version_form( $input );

        $this->assertTrue( $this->arrays_are_similar( $output, $expected_output['data'] ) );

//         TODO test retrieval from DB as well here
        $from_db = \kwps_classes\Version::get_with_all_children( $output['ID'] );
        $this->assertTrue( $this->arrays_are_similar( $expected_output['data'], $from_db ) );
    }


    /**
     * Determine if two associative arrays are similar
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    function arrays_are_similar($a, $b) {
        if(! is_array( $a) ) {
            return false;
        }

        if(! is_array( $b) ) {
            return false;
        }

        $sorted_a = $this->sort_array_by_key( $a );
        $sorted_b = $this->sort_array_by_key( $b );

        if ( $sorted_a === $sorted_b ) {
            return true;
        } else {
            var_dump( $sorted_a, $sorted_b);
            return false;
        }
    }

    function sort_array_by_key( $a ) {
        ksort( $a );

        foreach( $a as $key => $value ) {
            if( is_array( $value ) ) {
                $a[$key] = $this->sort_array_by_key( $value );
            }
        }

        return $a;
    }
}
