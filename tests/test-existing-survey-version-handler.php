<?php

require_once 'kwps-test.php';

class Existing_Survey_Version_Handler_Test extends Kwps_Test {

    function __construct() {
        parent::__construct();
        $this->test_modus_name = 'kwps-survey';
        $this->test_data_folder = __DIR__ . '/../form-test-data/existing-version/survey/';
    }

    function test_validate_remove_answer_option(){
        $this->checkOutPutWithFormTestData( 'removing-answer-option-test.php' );
    }

    function test_validate_remove_too_many_answer_options() {
        $this->checkOutPutWithFormTestData( 'removing-too-many-answer-options-test.php' );
    }

    function test_validate_remove_too_many_questions() {
        $this->checkOutPutWithFormTestData( 'removing-too-many-questions-test.php' );
    }

    function test_validate_remove_too_many_question_groups() {
        $this->checkOutPutWithFormTestData( 'removing-too-many-question-groups-test.php');
    }

    function test_save_trashed_answer_option() {
        $this->check_saved_and_updated_siblings( 'save-trashed-answer-option-test.php' );
    }

    function test_save_added_answer_option() {
        $this->check_saved_and_updated_siblings( 'save-added-answer-option-test.php');
    }

}

