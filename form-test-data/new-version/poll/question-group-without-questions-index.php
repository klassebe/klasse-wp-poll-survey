<?php
return array(
    'input' => array(
        'post_title' => 'New Version',
        'post_parent' => 4,
        'post_status' => 'draft',
        '_kwps_sort_order' => 0,
        '_kwps_view_count' => 0,
        'intro' => array(
            'post_content' => 'Intro contents',
            '_kwps_sort_order' => 0,
            'post_status' => 'draft',
        ),
        'intro_result' => array(
            'post_content' => 'Intro result contents',
            '_kwps_sort_order' => 0,
            'post_status' => 'draft',
        ),
        'outro' => array(
            'post_content' => 'Outro contents [kwps_result result=bar-chart-per-question]',
            '_kwps_sort_order' => 0,
            'post_status' => 'draft',
        ),
        'question_groups' => array(
            1 => array(
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'post_title' => 'Question page 1',
                'post_content' => 'These are the questions of page 1',
            ),

        ),
    ),
    'expected_output' => array(
        'errors' => true,
        'test_modus_errors' => array(),
        'data' => array(
            'post_title' => 'New Version',
            'post_parent' => 12,
            'post_status' => 'draft',
            '_kwps_sort_order' => 0,
            '_kwps_view_count' => 0,
            'errors' => array(),
            'intro' => array(
                'post_content' => 'Intro contents',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'errors' => array(),
            ),
            'intro_result' => array(
                'post_content' => 'Intro result contents',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'errors' => array(),
            ),
            'outro' => array(
                'post_content' => 'Outro contents [kwps_result result=bar-chart-per-question]',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
                'errors' => array(),
            ),
            'question_groups' => array(
                1 => array(
                    '_kwps_sort_order' => 0,
                    'post_status' => 'draft',
                    'post_title' => 'Question page 1',
                    'post_content' => 'These are the questions of page 1',
                    'questions' => array(
                        1 => array(
                            '_kwps_sort_order' => 0,
                            'post_status' => 'draft',
                            'post_content' => '',
                            'answer_options' => array(
                                1 => array(
                                    '_kwps_sort_order' => 0,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                    'errors' => array(
                                        'post_content' => 'Required',
                                    ),
                                ),
                                2 => array(
                                    '_kwps_sort_order' => 1,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                    'errors' => array(
                                        'post_content' => 'Required',
                                    ),
                                ),
                            ),
                            'errors' => array(
                                'post_content' => 'Required'
                            ),
                        ),
                    ),
                    'errors' => array(),
                ),

            ),
        ),
    ),
);