<?php
return array(
    'input' => array(
        'post_title' => 'New Version',
        'post_parent' => 12,
        'post_status' => 'draft',
        '_kwps_sort_order' => 1,
        'intro' => array(
            'post_content' => 'Intro contents',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
        ),
        'intro_result' => array(
            'post_content' => 'Intro result contents',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
        ),
        'outro' => array(
            'post_content' => 'Outro contents',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
        ),
        'result_profiles' => array(
            1 => array(
                'post_title' => 'Wolf',
                '_kwps_sort_order' => 1,
                '_kwps_min_value' => 0,
                '_kwps_max_value' => 15,
                'errors' => array(),
            ),
            2 => array(
                'post_title' => 'Bird',
                '_kwps_sort_order' => 2,
                '_kwps_min_value' => 16,
                '_kwps_max_value' => 30,
                'errors' => array(),
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
            '_kwps_sort_order' => 1,
            'intro' => array(
                'post_content' => 'Intro contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'errors' => array(),
            ),
            'intro_result' => array(
                'post_content' => 'Intro result contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'errors' => array(),
            ),
            'outro' => array(
                'post_content' => 'Outro contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'errors' => array(),
            ),
            'question_groups' => array(
                1 => array(
                    'errors' => array(),
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_title' => '',
                    'post_content' => '',
                    'questions' => array(
                        1 => array(
                            'errors' => array(
                                'post_content' => 'Required',
                            ),
                            '_kwps_sort_order' => 1,
                            'post_status' => 'draft',
                            'post_content' => '',
                            'answer_options' => array(
                                1 => array(
                                    'errors' => array(
                                        'post_content' => 'Required',
                                        '_kwps_answer_option_value' => 'Required',
                                    ),
                                    '_kwps_sort_order' => 1,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                                2 => array(
                                    'errors' => array(
                                        'post_content' => 'Required',
                                        '_kwps_answer_option_value' => 'Required',
                                    ),
                                    '_kwps_sort_order' => 2,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            'result_profiles' => array(
                1 => array(
                    'post_title' => 'Wolf',
                    '_kwps_sort_order' => 1,
                    '_kwps_min_value' => 0,
                    '_kwps_max_value' => 15,
                    'errors' => array(),
                ),
                2 => array(
                    'post_title' => 'Bird',
                    '_kwps_sort_order' => 2,
                    '_kwps_min_value' => 16,
                    '_kwps_max_value' => 30,
                    'errors' => array(),
                ),
            ),
            'errors' => array(),
        ),
    ),
);