<?php
return array(
    'input' => array(
        'ID' => 5,
        'post_title' => 'New Version',
        'post_parent' => 4,
        'post_status' => 'draft',
        '_kwps_sort_order' => 0,
        'intro' => array(
            'ID' => 6,
            'post_content' => 'Intro contents',
            '_kwps_sort_order' => 0,
            'post_status' => 'draft',
            'post_parent' => 5,
        ),
        'intro_result' => array(
            'ID' => 7,
            'post_content' => 'Intro result contents',
            '_kwps_sort_order' => 0,
            'post_status' => 'draft',
            'post_parent' => 5,
        ),
        'outro' => array(
            'ID' => 8,
            'post_content' => 'Outro contents [kwps_result result=bar-chart-per-question]',
            '_kwps_sort_order' => 0,
            'post_status' => 'draft',
            'post_parent' => 5,
        ),
        'question_groups' => array(
            0 => array(
                'ID' => 9,
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'post_title' => 'Question page 1',
                'post_content' => 'These are the questions of page 1',
                'post_parent' => 5,
                'questions' => array(
                    1 => array(
                        'ID' => 10,
                        '_kwps_sort_order' => 0,
                        'post_status' => 'draft',
                        'post_content' => 'Question 1',
                        'post_parent' => 9,
                        'answer_options' => array(
                            0 => array(
                                'ID' => 11,
                                '_kwps_sort_order' => 0,
                                'post_content' => 'Answer option 1',
                                'post_status' => 'draft',
                                'post_parent' => 10,
                            ),
                            1 => array(
                                'ID' => 12,
                                '_kwps_sort_order' => 1,
                                'post_content' => 'Answer option 2',
                                'post_status' => 'trash',
                                'post_parent' => 10,
                            ),
                            2 => array(
                                'ID' => 13,
                                '_kwps_sort_order' => 2,
                                'post_content' => 'Answer option 3',
                                'post_status' => 'draft',
                                'post_parent' => 10,
                            ),
                        ),
                    ),
                ),
            ),

        ),
    ),
    'expected_output' => array(
        'errors' => false,
        'test_modus_errors' => array(),
        'data' => array(
            'ID' => 5,
            'post_title' => 'New Version',
            'post_parent' => 4,
            'post_status' => 'draft',
            '_kwps_sort_order' => 0,
            '_kwps_view_count' => 0,
            'intro' => array(
                'ID' => 6,
                'post_content' => 'Intro contents',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'post_parent' => 5,
            ),
            'intro_result' => array(
                'ID' => 7,
                'post_content' => 'Intro result contents',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'post_parent' => 5,
            ),
            'outro' => array(
                'ID' => 8,
                'post_content' => 'Outro contents [kwps_result result=bar-chart-per-question]',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'post_parent' => 5,
            ),
            'question_groups' => array(
                0 => array(
                    'ID' => 9,
                    '_kwps_sort_order' => 0,
                    'post_status' => 'draft',
                    'post_title' => 'Question page 1',
                    'post_content' => 'These are the questions of page 1',
                    'post_parent' => 5,
                    'questions' => array(
                        0 => array(
                            'ID' => 10,
                            '_kwps_sort_order' => 0,
                            'post_status' => 'draft',
                            'post_content' => 'Question 1',
                            'post_parent' => 9,
                            'answer_options' => array(
                                0 => array(
                                    'ID' => 11,
                                    '_kwps_sort_order' => 0,
                                    'post_content' => 'Answer option 1',
                                    'post_status' => 'draft',
                                    'post_parent' => 10,
                                ),
                                1 => array(
                                    'ID' => 13,
                                    '_kwps_sort_order' => 1,
                                    'post_content' => 'Answer option 3',
                                    'post_status' => 'draft',
                                    'post_parent' => 10,
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
);