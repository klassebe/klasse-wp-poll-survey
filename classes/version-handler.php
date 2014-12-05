<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 23/10/14
 * Time: 08:58
 */

namespace kwps_classes;

require_once __DIR__ . '/post-types/version.php';
require_once __DIR__ . '/post-types/intro.php';
require_once __DIR__ . '/post-types/outro.php';
require_once __DIR__ . '/post-types/question_group.php';
require_once __DIR__ . '/post-types/question.php';
require_once __DIR__ . '/post-types/answer-option.php';


class Version_Handler {

    public static $top_level_indexes = array(
                                        'intro',
                                        'outro',
                                        'question_groups',
    );

    public function validate_new_version_form( $data ) {
        $data_has_errors = false;

        if(! is_array( $data ) ) {
            $data = array();
        }

        $data = $this->add_missing_top_level_indexes($data);

        $test_modus_errors = $this->validate_for_test_modus($data);

        if( sizeof( $test_modus_errors ) != 0 ) {
            $data_has_errors = true;
        }

        $stripped_version = array_diff_key( $data, array('question_groups' => '') );
        $version_errors = Version::validate_for_insert( $stripped_version, true);

        $data['errors'] = $version_errors;

        if( sizeof( $version_errors ) != 0 ) {
            $data_has_errors = true;
        }

        $intro_errors = Intro::validate_for_insert( $data['intro'] );
        $data['intro']['errors'] = $intro_errors;

        if( sizeof($intro_errors) != 0 ) {
            $data_has_errors = true;
        }

        $intro_result_errors = Intro_Result::validate_for_insert( $data['intro_result'] );
        $data['intro_result']['errors'] = $intro_result_errors;

        if( sizeof($intro_result_errors) != 0 ) {
            $data_has_errors = true;
        }

        $intro_errors = Outro::validate_for_insert( $data['outro'] );
        $data['outro']['errors'] = $intro_errors;

        if( sizeof($intro_errors) != 0 ) {
            $data_has_errors = true;
        }

        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            $stripped_question_group = array_diff_key($question_group, array( 'questions' => '' ) );
            $question_group_errors = Question_Group::validate_for_insert( $stripped_question_group );

            $data['question_groups'][$question_group_key]['errors'] = $question_group_errors;

            if( sizeof( $question_group_errors ) != 0 ) {
                $data_has_errors = true;
            }

            foreach( $question_group['questions'] as $question_key => $question ) {
                $stripped_question = array_diff_key($question, array( 'answer_options' => '' ) );
                $question_errors = Question::validate_for_insert($stripped_question);

                $data['question_groups'][$question_group_key]['questions'][$question_key]['errors'] = $question_errors;

                if( sizeof( $question_errors ) != 0 ) {
                    $data_has_errors = true;
                }
                foreach( $question['answer_options'] as $answer_option_key => $answer_option ) {
                    $answer_option_errors = Answer_Option::validate_for_insert($answer_option);

                    $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'][$answer_option_key]['errors'] = $answer_option_errors;

                    if( sizeof( $answer_option_errors ) != 0 ) {
                        $data_has_errors = true;
                    }
                }
            }
        }

        return array('errors' => $data_has_errors, 'test_modus_errors' => $test_modus_errors, 'data' => $data);
    }

    public function add_missing_top_level_indexes($data) {
        if(! isset( $data['post_title'] ) ) {
            $data = array_merge(
                $data,
                array('post_title' => '')
            );
        }

        if(! isset( $data['post_parent'] ) ) {
            $data = array_merge(
                $data,
                array('post_parent' => '')
            );
        }

        if(! isset( $data['post_status'] ) ) {
            $data = array_merge(
                $data,
                array('post_status' => 'draft')
            );
        }

        if(! isset( $data['_kwps_sort_order'] ) ) {
            $data = array_merge(
                $data,
                array('_kwps_sort_order' => 1)
            );
        }

        if(! isset( $data['intro'] ) ) {
            $data = array_merge(
                $data,
                array('intro' => array(
                                'post_content' => '',
                                '_kwps_sort_order' => 1,
                                'post_status' => 'draft',
                                )
                )
            );
        }

        if(! isset( $data['intro_result'] ) ) {
            $data = array_merge(
                $data,
                array('intro_result' => array(
                    'post_content' => '',
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                )
                )
            );
        }

        if(! isset( $data['outro'] ) ) {
            $data['outro'] = array(
                'post_content' => '',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            );
        }

        if(! isset( $data['question_groups'] ) ) {
            $data['question_groups'] = array(
                1 => array(
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_title' => '',
                    'post_content' => '',
                    'questions' => array(
                        1 => array(
                            '_kwps_sort_order' => 1,
                            'post_status' => 'draft',
                            'post_content' => '',
                            'answer_options' => array(
                                1 => array(
                                    '_kwps_sort_order' => 1,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                                2 => array(
                                    '_kwps_sort_order' => 2,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                            ),
                        ),
                    ),
                ),

            );
        }

        return $data;
    }

    public function validate_for_test_modus( $data ) {
        $test_modus_errors = array();

        if( ! empty( $data['post_parent'] ) ) {
            $test_modus = Test_Collection::get_test_modus( $data['post_parent'] );

            if( sizeof( $data['question_groups'] ) > $test_modus['_kwps_max_question_groups'] ) {
                $test_modus_errors['_kwps_max_question_groups'] = 'Only ' . $test_modus['_kwps_max_question_groups']
                    . ' question group(s) allowed';
            }

            foreach( $data['question_groups'] as $question_group ) {
                if( sizeof( $question_group['questions'] ) > $test_modus['_kwps_max_questions_per_question_group'] ) {
                    $test_modus_errors['_kwps_max_questions_per_question_group'] = 'Only ' . $test_modus['_kwps_max_questions_per_question_group']
                        . ' question(s) per group allowed';
                }
                foreach( $question_group['questions'] as $question ) {
                    if( isset( $question['answer_options'] ) ) {
                        if( sizeof( $question['answer_options'] ) < 2 ) {
                            $test_modus_errors['minimum_answer_options_per_question'] =
                                'At least 2 answer options per question';
                        }
                    } else {
                        $test_modus_errors['minimum_answer_options_per_question'] =
                            'At least 2 answer options per question';
                    }
                }
            }
        }

        return $test_modus_errors;
    }

    public function get_question_group_count( $data ) {
        return sizeof( $data['question_groups'] );
    }

    public function save_new_version_form($data){
//        var_dump( $data);
//        $data = $this->update_kwps_sort_order_of_form( $data );
//
//        var_dump( $data);die;

        $stripped_version = array_diff_key( $data, array('question_groups' => '') );
        $version_id = Version::save_post($stripped_version, true);

        $data['ID'] = $version_id;

        $data['intro']['post_parent'] = $version_id;
        $intro_id = Intro::save_post($data['intro'], true);
        $data['intro']['ID'] = $intro_id;

        $data['intro_result']['post_parent'] = $version_id;
        $intro_result_id = Intro_Result::save_post($data['intro_result'], true);
        $data['intro_result']['ID'] = $intro_result_id;

        $data['outro']['post_parent'] = $version_id;
        $outro_id = Outro::save_post($data['outro'], true);
        $data['outro']['ID'] = $outro_id;


        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            $stripped_question_group = array_diff_key($question_group, array( 'questions' => '' ) );
            $stripped_question_group['post_parent'] = $version_id;


            $question_group_id = Question_Group::save_post($stripped_question_group, true);
            $data['question_groups'][$question_group_key]['ID'] = $question_group_id;

            foreach( $question_group['questions'] as $question_key => $question ) {
                $stripped_question = array_diff_key($question, array( 'answer_options' => '' ) );
                $stripped_question['post_parent'] = $question_group_id;

                $question_id = Question::save_post( $stripped_question, true );
                $data['question_groups'][$question_group_key]['questions'][$question_key]['ID'] = $question_group_id;

                foreach( $question['answer_options'] as $answer_option_key => $answer_option ) {
                    if( 'trash' == $answer_option['post_status'] ) {
                        unset( $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'][$answer_option_key] );
                        if( isset( $answer_option['ID'] ) ) {
                            wp_delete_post( $answer_option['ID'], true );
                        }
                    } else {
                        $answer_option['post_parent'] = $question_id;

                        $answer_option_id = Answer_Option::save_post( $answer_option, true );
                        $answer_option['ID'] = $answer_option_id;
                        $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'][$answer_option_key]['ID'] = $answer_option_id;
                    }
                }
            }
        }

        return $data;
    }

    private function update_kwps_sort_order_of_form( $data ) {
        $data['question_groups'] = $this->update_kwps_sort_order( $data['question_groups'] );

        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            $data['question_groups'][$question_group_key]['questions'] =
                $this->update_kwps_sort_order($data['question_groups'][$question_group_key]['questions']);

            foreach( $question_group['questions'] as $question_key => $question ) {
                $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'] =
                    $this->update_kwps_sort_order( $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'] );
            }
        }

        return $data;
    }

    private function update_kwps_sort_order( $data ) {
        $trashed_items = $this->get_trashed_item_indexes( $data );

        $tempData = array();

        foreach( $data as $index => $item ) {
            if( 'trash' != $item['post_status'] ) {
                $tempData[] = $item;
            }
        }

        foreach( $tempData as $index => $item ) {
            $tempData[$index]['_kwps_sort_order'] = $index;
        }

        foreach( $trashed_items as $index ) {
            $tempData[] = $data[$index];
        }

        return $tempData;

    }

    private function get_trashed_item_indexes( $data ) {
        $indexes = array();

        foreach( $data as $index => $item ) {
            if( 'trash' == $item['post_status'] ) {
                $indexes[] = $index;
            }
        }

        return $indexes;
    }

    public function validate_existing_version_form( $data ) {
        $data_has_errors = false;

        $stripped_version = array_diff_key( $data, array('question_groups' => '') );
        $version_errors = Version::validate_for_update($stripped_version);

        $data['errors'] = $version_errors;

        if( sizeof( $version_errors ) != 0 ) {
            $data_has_errors = true;
        }

        $intro_errors = Intro::validate_for_update( $data['intro'] );
        $data['intro']['errors'] = $intro_errors;

        if( sizeof($intro_errors) != 0 ) {
            $data_has_errors = true;
        }

        $intro_result_errors = Intro::validate_for_update( $data['intro_result'] );
        $data['intro_result']['errors'] = $intro_result_errors;

        if( sizeof($intro_result_errors) != 0 ) {
            $data_has_errors = true;
        }

        $outro_errors = Outro::validate_for_update( $data['outro'] );
        $data['outro']['errors'] = $outro_errors;

        if( sizeof($outro_errors) != 0 ) {
            $data_has_errors = true;
        }

        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            $stripped_question_group = array_diff_key($question_group, array( 'questions' => '' ) );
            $question_group_errors = Question_Group::validate_for_update( $stripped_question_group );

            $data['question_groups'][$question_group_key]['errors'] = $question_group_errors;

            if( sizeof( $question_group_errors ) != 0 ) {
                $data_has_errors = true;
            }

            foreach( $question_group['questions'] as $question_key => $question ) {
                $stripped_question = array_diff_key($question, array( 'answer_options' => '' ) );
                $question_errors = Question::validate_for_update($stripped_question);

                $data['question_groups'][$question_group_key]['questions'][$question_key]['errors'] = $question_errors;

                if( sizeof( $question_errors ) != 0 ) {
                    $data_has_errors = true;
                }

                foreach( $question['answer_options'] as $answer_option_key => $answer_option ) {
                    $answer_option_errors = Answer_Option::validate_for_update($answer_option);

                    $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'][$answer_option_key]['errors'] = $answer_option_errors;

                    if( sizeof( $answer_option_errors ) != 0 ) {
                        $data_has_errors = true;
                    }
                }
            }
        }

        return array('errors' => $data_has_errors, 'data' => $data);
    }


    private function save_question( $question_group_id, $question ) {
        $data = array(
            'post_content' => $question['post_content'],
            '_kwps_sort_order' => $question['_kwps_sort_order'],
            'post_parent' => $question_group_id,
        );

        $question_id = Question::save_post($data, true);

        foreach( $question['answer_options'] as $answer_option ) {
            $this->save_answer_option( $question_id, $answer_option);
        }
    }

    private function save_answer_option( $question_id, $answer_option ) {
        $data = array(
            'post_content' => $answer_option['post_content'],
            '_kwps_sort_order' => $answer_option['_kwps_sort_order'],
            'post_parent' => $question_id,
        );
        $answer_option_id = Answer_Option::save_post($data, true);
    }
} 