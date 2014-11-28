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
        'outro' => array(
            'post_content' => 'Outro contents',
            '_kwps_sort_order' => 1,
            'post_status' => 'draft',
        ),
    ),
    'expected_output' => array(
        'errors' => true,
        'data' => array(
            'post_title' => 'New Version',
            'post_parent' => 12,
            'post_status' => 'draft',
            '_kwps_sort_order' => 1,
            'intro' => array(
                'post_content' => 'Intro contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            ),
            'outro' => array(
                'post_content' => 'Outro contents',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            ),
            'question_groups' => array(
                1 => array(
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
                                    ),
                                    '_kwps_sort_order' => 1,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                                2 => array(
                                    'errors' => array(
                                        'post_content' => 'Required',
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
        ),
    ),
);