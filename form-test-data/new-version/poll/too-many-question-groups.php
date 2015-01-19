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
            'post_content' => 'Outro contents [kwps_result result=bar-chart-per-question]',
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
                        ),
                    ),
                ),
            ),
            2 => array(
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'post_title' => 'Question page 2',
                'post_content' => 'These are the questions of page 2',
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
                        ),
                    ),
                ),
            ),
            3 => array(
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
                'post_title' => 'Question page 3',
                'post_content' => 'These are the questions of page 3',
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
                        ),
                    ),
                ),
            ),
        ),
    ),
    'expected_output' => array(
        'errors' => true,
        'test_modus_errors' => array(
            '_kwps_max_question_groups' => 'Only 1 question group(s) allowed',
        ),
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
                'post_content' => 'Outro contents [kwps_result result=bar-chart-per-question]',
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
                    'errors' => array(),
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
                                    'errors' => array(),
                                ),
                                2 => array(
                                    '_kwps_sort_order' => 2,
                                    'post_content' => 'Answer option 2',
                                    'post_status' => 'draft',
                                    'errors' => array(),
                                ),
                            ),
                            'errors' => array(),
                        ),
                    ),
                ),
                2 => array(
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_title' => 'Question page 2',
                    'post_content' => 'These are the questions of page 2',
                    'errors' => array(),
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
                                    'errors' => array(),
                                ),
                                2 => array(
                                    '_kwps_sort_order' => 2,
                                    'post_content' => 'Answer option 2',
                                    'post_status' => 'draft',
                                    'errors' => array(),
                                ),
                            ),
                            'errors' => array(),
                        ),
                    ),
                ),
                3 => array(
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_title' => 'Question page 3',
                    'post_content' => 'These are the questions of page 3',
                    'errors' => array(),
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
                                    'errors' => array(),
                                ),
                                2 => array(
                                    '_kwps_sort_order' => 2,
                                    'post_content' => 'Answer option 2',
                                    'post_status' => 'draft',
                                    'errors' => array(),
                                ),
                            ),
                            'errors' => array(),
                        ),
                    ),
                ),
            ),
            'errors' => array(),
        ),
    ),
);