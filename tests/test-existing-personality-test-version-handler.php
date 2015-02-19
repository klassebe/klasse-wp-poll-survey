<?php

require_once 'kwps-test.php';

class Existing_Personality_Test_Version_Handler_Test extends Kwps_Test {

    function __construct() {
        parent::__construct();
        $this->test_modus_name = 'kwps-personality-test';
        $this->test_data_folder = __DIR__ . '/../form-test-data/existing-version/personality-test/';
    }

//    function test_validate_remove_answer_option(){
//        $this->checkOutPutWithFormTestData( 'removing-answer-option-test.php' );
//    }
//
//    function test_validate_remove_result_profile() {
//        $this->checkOutPutWithFormTestData( 'removing-result-profile-test.php' );
//    }
//
//    function test_validate_remove_too_many_answer_options() {
//        $this->checkOutPutWithFormTestData( 'removing-too-many-answer-options-test.php' );
//    }
//
//    function test_validate_remove_too_many_questions() {
//        $this->checkOutPutWithFormTestData( 'removing-too-many-questions-test.php' );
//    }
//
//    function test_validate_remove_too_many_question_groups() {
//        $this->checkOutPutWithFormTestData( 'removing-too-many-question-groups-test.php');
//    }
//
//    function test_validate_add_answer_option_without_value() {
//        $this->checkOutPutWithFormTestData( 'add-answer-option-without-value.php');
//    }
//
//    function test_validate_add_answer_option_invalid_value() {
//            $this->checkOutPutWithFormTestData( 'add-answer-option-invalid-value.php');
//    }

    function test_save_trashed_answer_option() {
        $input = $this->existing_versions[0];
        $input['question_groups'][1]['questions'][1]['answer_options'][2]['post_status'] = 'trash';

        $this->check_saved_and_updated_siblings( $input, 'save-trashed-answer-option-test.php' );
    }

    function test_save_added_answer_option() {
        $input = $this->existing_versions[0];
        $input['question_groups'][1]['questions'][1]['answer_options'][4] = array(
            '_kwps_sort_order' => 3,
            'post_content' => 'Answer option 4',
            'post_status' => 'draft',
            '_kwps_answer_option_value' => 20,
//            'post_parent' => 13,
        );

        $this->check_saved_and_updated_siblings( $input, 'save-added-answer-option-test.php' );
    }

}

