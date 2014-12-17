<?php
return array(
    'input' => array(
        'post_title' => 'New Version',
        'post_parent' => 4,
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
                        'answer_options' => array(
                            1 => array(
                                '_kwps_sort_order' => 1,
                                'post_content' => 'Answer option 1',
                                'post_status' => 'draft',
                            ),
                            2 => array(
                                '_kwps_sort_order' => 2,
                                'post_content' => 'Answer option 2',
                                'post_status' => 'draft',
                            ),
                            3 => array(
                                '_kwps_sort_order' => 3,
                                'post_content' => 'Answer option 3',
                                'post_status' => 'draft',
                            ),
                        ),
                    ),
                ),
            ),

        ),
    ),
    'expected_output' => array(
        'data' => array(
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
                                ),
                                2 => array(
                                    'ID' => 12,
                                    '_kwps_sort_order' => 2,
                                    'post_content' => 'Answer option 2',
                                    'post_status' => 'draft',
                                    'post_parent' => 10,
                                ),
                                3 => array(
                                    'ID' => 13,
                                    '_kwps_sort_order' => 3,
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