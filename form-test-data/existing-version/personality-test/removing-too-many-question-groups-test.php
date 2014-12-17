<?php
return array(
    'input' => array(
        'ID' => 5,
        'post_title' => 'New Version',
        'post_parent' => 4,
        'post_status' => 'draft',
        '_kwps_sort_order' => 1,
        'intro' => array(
            'ID' => 6,
            'post_content' => 'Intro contents',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
            'post_parent' => 5,
        ),
        'intro_result' => array(
            'ID' => 7,
            'post_content' => 'Intro result contents',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
            'post_parent' => 5,
        ),
        'outro' => array(
            'ID' => 8,
            'post_content' => 'Outro contents',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
            'post_parent' => 5,
        ),
        'question_groups' => array(
            1 => array(
                'ID' => 9,
                '_kwps_sort_order' => 1,
                'post_status' => 'trash',
                'post_title' => 'Question page 1',
                'post_content' => 'These are the questions of page 1',
                'post_parent' => 5,
                'questions' => array(
                    1 => array(
                        'ID' => 10,
                        '_kwps_sort_order' => 1,
                        'post_status' => 'draft',
                        'post_content' => 'Question 1',
                        'post_parent' => 9,
                        'answer_options' => array(
                            1 => array(
                                'ID' => 11,
                                '_kwps_sort_order' => 1,
                                'post_content' => 'Answer option 1',
                                'post_status' => 'draft',
                                'post_parent' => 10,
                                '_kwps_answer_option_value' => 5,
                            ),
                            2 => array(
                                'ID' => 12,
                                '_kwps_sort_order' => 2,
                                'post_content' => 'Answer option 2',
                                'post_status' => 'draft',
                                'post_parent' => 10,
                                '_kwps_answer_option_value' => 10,
                            ),
                        ),
                    ),
                ),
            ),

        ),
    ),
    'expected_output' => array(
        'errors' => true,
        'test_modus_errors' => array(
            '_kwps_min_question_groups_per_version' => 'Minimum 1 question group required per version',
        ),
        'data' => array(
            'ID' => 5,
            'post_title' => 'New Version',
            'post_parent' => 4,
            'post_status' => 'draft',
            '_kwps_sort_order' => 1,
            'errors' => array(),
            'intro' => array(
                'ID' => 6,
                'post_content' => 'Intro contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'post_parent' => 5,
                'errors' => array(),
            ),
            'intro_result' => array(
                'ID' => 7,
                'post_content' => 'Intro result contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'post_parent' => 5,
                'errors' => array(),
            ),
            'outro' => array(
                'ID' => 8,
                'post_content' => 'Outro contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'post_parent' => 5,
                'errors' => array(),
            ),
            'question_groups' => array(
                1 => array(
                    'ID' => 9,
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_title' => 'Question page 1',
                    'post_content' => 'These are the questions of page 1',
                    'post_parent' => 5,
                    'questions' => array(
                        1 => array(
                            'ID' => 10,
                            '_kwps_sort_order' => 1,
                            'post_status' => 'draft',
                            'post_content' => 'Question 1',
                            'post_parent' => 9,
                            'answer_options' => array(
                                1 => array(
                                    'ID' => 11,
                                    '_kwps_sort_order' => 1,
                                    'post_content' => 'Answer option 1',
                                    'post_status' => 'draft',
                                    'post_parent' => 10,
                                    '_kwps_answer_option_value' => 5,
                                    'errors' => array(),
                                ),
                                2 => array(
                                    'ID' => 12,
                                    '_kwps_sort_order' => 2,
                                    'post_content' => 'Answer option 2',
                                    'post_status' => 'draft',
                                    'post_parent' => 10,
                                    '_kwps_answer_option_value' => 10,
                                    'errors' => array(
                                    ),
                                ),
                            ),
                            'errors' => array(),
                        ),
                    ),
                    'errors' => array(
                        'post_status' => 'Minimum 1 question group required per version'
                    ),
                ),
            ),
        ),
    ),
);