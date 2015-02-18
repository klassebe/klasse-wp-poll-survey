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
        $input = $this->existing_versions[0];
        $input['question_groups'][1]['questions'][1]['answer_options'][2]['post_status'] = 'trash';
        $input['question_groups'][1]['questions'][3]['answer_options'][3]['post_status'] = 'trash';
        $input['question_groups'][3]['questions'][1]['answer_options'][1]['post_status'] = 'trash';

        $this->check_saved_and_updated_siblings( $input, 'save-trashed-answer-options-test.php' );
    }

    function test_save_added_answer_option() {
        $input = $this->existing_versions[0];
        $input['question_groups'][1]['questions'][1]['answer_options'][4] = array(
            '_kwps_sort_order' => 3,
            'post_content' => 'Answer option 4',
            'post_status' => 'draft',
            'post_parent' => 10,
        );

        $this->check_saved_and_updated_siblings( $input, 'save-added-answer-option-test.php' );
    }

    function test_save_trashed_question() {
        $input = $this->existing_versions[0];
        $input['question_groups'][2]['questions'][1]['post_status'] = 'trash';

        $this->check_saved_and_updated_siblings( $input, 'save-trashed-questions-test.php' );
    }

    function test_save_added_question() {
        $input = $this->existing_versions[0];

        $input['question_groups'][1]['questions'][] =
            array(
                '_kwps_sort_order' => 3,
                'post_status' => 'draft',
                'post_content' => 'Question 4 - new',
                'post_parent' => 9,
                'answer_options' => array(
                    0 => array(
                        '_kwps_sort_order' => 0,
                        'post_content' => 'Answer option 1',
                        'post_status' => 'draft',
                    ),
                    1 => array(
                        '_kwps_sort_order' => 1,
                        'post_content' => 'Answer option 2',
                        'post_status' => 'draft',
                    ),
                    2 => array(
                        '_kwps_sort_order' => 2,
                        'post_content' => 'Answer option 3',
                        'post_status' => 'draft',
                    ),
                ),
        );

        $this->check_saved_and_updated_siblings( $input, 'save-added-question-test.php' );
    }

    function test_save_added_question_group() {
        $input = $this->existing_versions[0];
        $input['question_groups'][] = array(
            '_kwps_sort_order' => 3,
            'post_status' => 'draft',
            'post_title' => 'Question page 4',
            'post_content' => 'These are the questions of page 4',
            'questions' => array(
                0 => array(
                    '_kwps_sort_order' => 0,
                    'post_status' => 'draft',
                    'post_content' => 'Question 1',
                    'answer_options' => array(
                        0 => array(
                            '_kwps_sort_order' => 0,
                            'post_content' => 'Answer option 1',
                            'post_status' => 'draft',
                        ),
                        1 => array(
                            '_kwps_sort_order' => 1,
                            'post_content' => 'Answer option 2',
                            'post_status' => 'draft',
                        ),
                        2 => array(
                            '_kwps_sort_order' => 2,
                            'post_content' => 'Answer option 3',
                            'post_status' => 'draft',
                        ),
                    ),
                ),
                1 => array(
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_content' => 'Question 2',
                    'answer_options' => array(
                        0 => array(
                            '_kwps_sort_order' => 0,
                            'post_content' => 'Answer option 1',
                            'post_status' => 'draft',
                        ),
                        1 => array(
                            '_kwps_sort_order' => 1,
                            'post_content' => 'Answer option 2',
                            'post_status' => 'draft',
                        ),
                        2 => array(
                            '_kwps_sort_order' => 2,
                            'post_content' => 'Answer option 3',
                            'post_status' => 'draft',
                        ),
                    ),
                ),
                2 => array(
                    '_kwps_sort_order' => 2,
                    'post_status' => 'draft',
                    'post_content' => 'Question 3',
                    'answer_options' => array(
                        0 => array(
                            '_kwps_sort_order' => 0,
                            'post_content' => 'Answer option 1',
                            'post_status' => 'draft',
                        ),
                        1 => array(
                            '_kwps_sort_order' => 1,
                            'post_content' => 'Answer option 2',
                            'post_status' => 'draft',
                        ),
                        2 => array(
                            '_kwps_sort_order' => 2,
                            'post_content' => 'Answer option 3',
                            'post_status' => 'draft',
                        ),
                    ),
                ),
            ),
        );

        $this->check_saved_and_updated_siblings( $input, 'save-added-question-group.php' );
    }

    function test_save_changed_sort_order_question() {
        $input = $this->existing_versions[0];
        $input['question_groups'][1]['questions'][1]['_kwps_new_sort_order'] = 1;
        $input['question_groups'][1]['questions'][2]['_kwps_new_sort_order'] = 0;

        $this->check_saved_and_updated_siblings( $input, 'save-changed-sort-order-question.php');
    }

}

