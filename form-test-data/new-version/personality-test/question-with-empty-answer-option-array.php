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
            'post_content' => 'Outro contents [kwps_result result=result-profile]',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
        ),
        'question_groups' => array(
            1 => array(
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'post_title' => 'Question page 1',
                'post_content' => 'These are the questions of page 1',
                'questions' => array(
                    1 => array(
                        '_kwps_sort_order' => 1,
                        'post_status' => 'draft',
                        'post_content' => 'Question 1',
                        'answer_options' => array(),
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
    ),
    'expected_output' => array(
        'errors' => true,
        'test_modus_errors' => array('minimum_answer_options_per_question' => 'At least 2 answer options per question'),
        'data' => array(
            'post_title' => 'New Version',
            'post_parent' => 12,
            'post_status' => 'draft',
            '_kwps_sort_order' => 1,
            'errors' => array(),
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
                'post_content' => 'Outro contents [kwps_result result=result-profile]',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'errors' => array(),
            ),
            'question_groups' => array(
                1 => array(
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_title' => 'Question page 1',
                    'post_content' => 'These are the questions of page 1',
                    'questions' => array(
                        1 => array(
                            '_kwps_sort_order' => 1,
                            'post_status' => 'draft',
                            'post_content' => 'Question 1',
                            'answer_options' => array(),
                            'errors' => array(),
                        ),
                    ),
                    'errors' => array(),
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
        ),
    ),
);