<?php
return array(
    'input' => array(),
    'expected_output' => array(
        'errors' => true,
        'test_modus_errors' => array(),
        'data' => array(
            'errors' => array(
                'post_title' => 'Required',
                'post_parent' => 'Required',
            ),
            'post_title' => '',
            'post_parent' => '',
            'post_status' => 'draft',
            '_kwps_sort_order' => 1,
            'intro' => array(
                'errors' => array(
                    'post_content' => 'Required',
                ),
                'post_content' => '',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            ),
            'intro_result' => array(
                'errors' => array(
                    'post_content' => 'Required',
                ),
                'post_content' => '',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            ),
            'outro' => array(
                'errors' => array(
                    'post_content' => 'Required',
                ),
                'post_content' => '',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            ),
            'question_groups' => array(
                1 => array(
                    'errors' => array(),
                    '_kwps_sort_order' => 0,
                    'post_status' => 'draft',
                    'post_title' => '',
                    'post_content' => '',
                    'questions' => array(
                        1 => array(
                            'errors' => array(
                                'post_content' => 'Required',
                            ),
                            '_kwps_sort_order' => 0,
                            'post_status' => 'draft',
                            'post_content' => '',
                            'answer_options' => array(
                                1 => array(
                                    'errors' => array(
                                        'post_content' => 'Required',
                                    ),
                                    '_kwps_sort_order' => 0,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                                2 => array(
                                    'errors' => array(
                                        'post_content' => 'Required',
                                    ),
                                    '_kwps_sort_order' => 1,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        ),
    )
);