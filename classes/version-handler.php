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

    protected $existing_version_data;

    protected $version_id;

    protected $question_group_key;
    protected $question_group_id;

    protected $question_key;
    protected $question_id;

    protected $answer_option_key;

    protected $sort_orders_to_update;

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
        $test_modus = Test_Collection::get_test_modus( $stripped_version['post_parent'], false );

        if( isset( $stripped_version['post_parent'] ) ) {
            $answer_options_require_value = ($test_modus['_kwps_answer_options_require_value'] > 0);
        } else {
            $answer_options_require_value = false;
        }

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

        $outro_errors = Outro::validate_for_insert( $data['outro'] );

        if( ! Outro::has_valid_result_code_in_post_content( $data['outro'], $test_modus ) ) {
            if( ! isset( $outro_errors['post_content']) ) {
                $outro_errors['post_content'] = 'No valid result code used';
            }
        }

        $data['outro']['errors'] = $outro_errors;

        if( sizeof($outro_errors) != 0 ) {
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

                    if( $answer_options_require_value ) {
                        if(! isset($answer_option['_kwps_answer_option_value'])) {
                            $answer_option_errors['_kwps_answer_option_value'] = 'Required';
                        } else {
                            if( is_string($answer_option['_kwps_answer_option_value'])){
                                if( strlen($answer_option['_kwps_answer_option_value']) == 0 ) {
                                    $answer_option_errors['_kwps_answer_option_value'] = 'Required';
                                }
                            }
                        }

                        if( isset( $answer_option['_kwps_answer_option_value']) ) {
                            if(! is_numeric( $answer_option['_kwps_answer_option_value'] ) ){
                                $answer_option_errors['_kwps_answer_option_value'] = 'Needs to be a number';
                            }
                        }
                    }

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
                    '_kwps_sort_order' => 0,
                    'post_status' => 'draft',
                    'post_title' => '',
                    'post_content' => '',
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
                                ),
                                2 => array(
                                    '_kwps_sort_order' => 1,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                            ),
                        ),
                    ),
                ),

            );
        }

        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            if(! isset( $question_group['questions'] ) ) {
                $data['question_groups'][$question_group_key]['questions'] = array(
                    1 => array(
                        '_kwps_sort_order' => 0,
                        'post_status' => 'draft',
                        'post_content' => '',
                        'answer_options' => array(
                            1 => array(
                                '_kwps_sort_order' => 0,
                                'post_content' => '',
                                'post_status' => 'draft',
                            ),
                            2 => array(
                                '_kwps_sort_order' => 1,
                                'post_content' => '',
                                'post_status' => 'draft',
                            ),
                        ),
                    ),
                );
            } else {
                foreach( $question_group['questions'] as $question_key => $question ) {
                    if( ! isset( $question['answer_options'] ) ) {
                        $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'] = array(
                            1 => array(
                                '_kwps_sort_order' => 0,
                                'post_content' => '',
                                'post_status' => 'draft',
                            ),
                            2 => array(
                                '_kwps_sort_order' => 1,
                                'post_content' => '',
                                'post_status' => 'draft',
                            ),
                        );
                    }
                }
            }
        }

        $test_modus = Test_Collection::get_test_modus( $data['post_parent'] );

        if( 'kwps-personality-test' == $test_modus['post_name'] && ! isset( $data['result_profiles'] ) ) {
            $data['result_profiles'] = array(
                1 => array(
                    'post_title' => '',
                    '_kwps_sort_order' => 1,
                    '_kwps_min_value' => '',
                    '_kwps_max_value' => '',
                    'errors' => array(
                        'post_title' => 'Required',
                        'post_parent' => 'Required',
                        '_kwps_min_value' => 'Required',
                        '_kwps_max_value' => 'Required',
                    ),
                ),
                2 => array(
                    'post_title' => '',
                    '_kwps_sort_order' => 2,
                    '_kwps_min_value' => '',
                    '_kwps_max_value' => '',
                    'errors' => array(
                        'post_title' => 'Required',
                        'post_parent' => 'Required',
                        '_kwps_min_value' => 'Required',
                        '_kwps_max_value' => 'Required',
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

            if( $test_modus['_kwps_max_question_groups'] > 0 ) {
                if( sizeof( $data['question_groups'] ) > $test_modus['_kwps_max_question_groups'] ) {
                    $test_modus_errors['_kwps_max_question_groups'] = 'Only ' . $test_modus['_kwps_max_question_groups']
                        . ' question group(s) allowed';
                }
            }


            foreach( $data['question_groups'] as $question_group ) {
                if( $test_modus['_kwps_max_questions_per_question_group'] > 0 ) {
                    if( sizeof( $question_group['questions'] ) > $test_modus['_kwps_max_questions_per_question_group'] ) {
                        $test_modus_errors['_kwps_max_questions_per_question_group'] = 'Only ' . $test_modus['_kwps_max_questions_per_question_group']
                            . ' question(s) per group allowed';
                    }
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
        $stripped_version = array_diff_key( $data, array('question_groups' => '') );
        $version_id = Version::save_post($stripped_version, true);
        if( ! isset( $stripped_version['_kwps_view_count'] ) ) {
            $data['_kwps_view_count'] = 0;
        }

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

        if( isset( $data['result_profiles'] ) ) {
            foreach( $data['result_profiles'] as $result_profile_key => $result_profile ) {
                $data['result_profiles'][$result_profile_key]['post_parent'] = $version_id;
                $profile_id = Result_Profile::save_post( $data['result_profiles'][$result_profile_key], true );
                $data['result_profiles'][$result_profile_key]['ID'] = $profile_id;
            }
        }


        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            $stripped_question_group = array_diff_key($question_group, array( 'questions' => '' ) );
            $stripped_question_group['post_parent'] = $version_id;


            $question_group_id = Question_Group::save_post($stripped_question_group, true);
            $data['question_groups'][$question_group_key]['ID'] = $question_group_id;
            $data['question_groups'][$question_group_key]['post_parent'] = $version_id;

            foreach( $question_group['questions'] as $question_key => $question ) {
                $stripped_question = array_diff_key($question, array( 'answer_options' => '' ) );
                $stripped_question['post_parent'] = $question_group_id;

                $question_id = Question::save_post( $stripped_question, true );
                $data['question_groups'][$question_group_key]['questions'][$question_key]['ID'] = $question_id;
                $data['question_groups'][$question_group_key]['questions'][$question_key]['post_parent'] = $question_group_id;

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
                        $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'][$answer_option_key]['post_parent'] = $question_id;
                    }
                }
            }
        }

        return $data;
    }

    public function save_existing_version_form($passed_data, $update_siblings = true){
        $this->sort_orders_to_update = array();

        $this->existing_version_data = $this->update_kwps_sort_order_of_form( $passed_data );

        $this->save_existing_version();
        $this->existing_version_data['ID'] = $this->version_id;

        $this->save_intro_of_existing_version();
        $this->save_intro_result_of_existing_version();
        $this->save_outro_of_existing_version();

        if( isset( $this->existing_version_data['result_profiles'] ) ) {
            foreach( $this->existing_version_data['result_profiles'] as $result_profile_key => $result_profile ) {
                $this->result_profile_key = $result_profile_key;
                $this->save_result_profile_of_existing_version( $result_profile );
            }
        }

        foreach( $this->existing_version_data['question_groups'] as $question_group_key => $question_group ) {
            $this->question_group_key = $question_group_key;
            $this->save_question_group_of_existing_version( $question_group );
        }

        if( $update_siblings ) {
            $matching_versions = Version::get_all_by_post_parent( $this->existing_version_data['post_parent'] );
            foreach( $matching_versions as $matched_version ) {
                $version_handler = new Version_Handler();
                if( $this->version_id != $matched_version['ID'] ) {
                    $sibling = Version::get_with_all_children( $matched_version['ID'] );
                    $version_handler->save_existing_version_form( $sibling, false);
                }
            }


            foreach( $this->sort_orders_to_update as $sort_order_data ) {
                update_post_meta( $sort_order_data[0], '_kwps_sort_order', $sort_order_data[1] );
            }
        }

        return Version::get_with_all_children( $this->version_id );
    }

    private function save_existing_version() {
        $stripped_version = array_diff_key( $this->existing_version_data, array('question_groups' => '') );
        $this->version_id = Version::save_post($stripped_version, true);
        if( ! isset( $stripped_version['_kwps_view_count'] ) ) {
            $this->existing_version_data['_kwps_view_count'] = 0;
        }
    }

    private function save_intro_of_existing_version() {
        $this->existing_version_data['intro']['post_parent'] = $this->version_id;
        $intro_id = Intro::save_post($this->existing_version_data['intro'], true);
        $this->existing_version_data['intro']['ID'] = $intro_id;
    }

    private function save_intro_result_of_existing_version() {
        $this->existing_version_data['intro_result']['post_parent'] = $this->version_id;
        $intro_result_id = Intro_Result::save_post($this->existing_version_data['intro_result'], true);
        $this->existing_version_data['intro_result']['ID'] = $intro_result_id;
    }

    private function save_outro_of_existing_version() {
        $this->existing_version_data['outro']['post_parent'] = $this->version_id;
        $outro_id = Outro::save_post($this->existing_version_data['outro'], true);
        $this->existing_version_data['outro']['ID'] = $outro_id;
    }

    private function save_result_profile_of_existing_version( $result_profile ) {
        if( 'trash' == $result_profile['post_status'] ) {
            if( isset( $result_profile['ID'] ) ) {
                Result_Profile::set_matching_to_trash( $result_profile['ID'] );
                wp_delete_post( $result_profile['ID'], true );
            }
        } else {
            if( isset( $result_profile['ID'] ) ) {
                $old_min_value = get_post_meta( $result_profile['ID'], '_kwps_min_value', true);
                $old_max_value = get_post_meta( $result_profile['ID'], '_kwps_max_value', true);

                if( $result_profile['_kwps_min_value'] != $old_min_value || $result_profile['_kwps_max_value'] != $old_max_value) {
                    foreach( Result_Profile::get_matches_in_other_versions( $result_profile['ID']) as $result_profile_id ) {
                        update_post_meta( $result_profile_id, '_kwps_min_value', $result_profile['_kwps_min_value'] );
                        update_post_meta( $result_profile_id, '_kwps_max_value', $result_profile['_kwps_max_value'] );
                    }
                }
            }

            $result_profile['post_parent'] = $this->version_id;
            Result_Profile::save_post( $result_profile );

            if( ! isset( $result_profile['ID'] ) ) {
                $matching_version_ids = Version::get_other_ids_in_parent( $this->version_id );

                foreach( $matching_version_ids as $version_id ) {
                    $new_result_profile = $result_profile;
                    $new_result_profile['post_parent'] = $version_id;
                    Result_Profile::save_post( $new_result_profile, true );
                }
            }
        }
    }

    private function save_question_group_of_existing_version( $question_group ) {
        if( 'trash' == $question_group['post_status'] ) {
            if( isset( $question_group['ID'] ) ) {
                Question_Group::set_matching_to_trash( $question_group['ID'] );
                wp_delete_post( $question_group['ID'], true ); // this will also delete all child posts
            }
        } else {
            $stripped_question_group = array_diff_key($question_group, array( 'questions' => '' ) );
            $stripped_question_group['post_parent'] = $this->version_id;

            if( isset( $stripped_question_group['_kwps_new_sort_order'] ) ) {
                $this->sort_orders_to_update[] = array( $stripped_question_group['ID'], $stripped_question_group['_kwps_new_sort_order'] );
                $matching_question_group_ids = Question_Group::get_matches_in_other_versions( $stripped_question_group['ID'] );
                foreach( $matching_question_group_ids as $question_id ) {
                    $this->sort_orders_to_update[] = array( $question_id, $stripped_question_group['_kwps_new_sort_order'] );
                }

                unset( $stripped_question_group['_kwps_new_sort_order'] );
            }

            $this->question_group_id = Question_Group::save_post($stripped_question_group, true);

            if( ! isset( $question_group['ID'] ) ) {
                $matching_version_ids = Version::get_other_ids_in_parent( $this->version_id );

                foreach( $matching_version_ids as $version_id ) {
                    $new_question_group = $stripped_question_group;
                    $new_question_group['post_parent'] = $version_id;
                    Question_Group::save_post( $new_question_group, true );
                }
            }

            foreach( $question_group['questions'] as $question_key => $question ) {
                $this->question_key = $question_key;
                $this->save_question_of_existing_version( $question );
            }
        }
    }

    private function save_question_of_existing_version( $question ) {
        if( 'trash' == $question['post_status'] ) {
            if( isset( $question['ID'] ) ) {
                Question::set_matching_to_trash( $question['ID'] );
                wp_delete_post( $question['ID'], true );
            }
        } else {
            $stripped_question = array_diff_key($question, array( 'answer_options' => '' ) );
            $stripped_question['post_parent'] = $this->question_group_id;

            if( isset( $stripped_question['_kwps_new_sort_order'] ) ) {
                $this->sort_orders_to_update[] = array( $stripped_question['ID'], $stripped_question['_kwps_new_sort_order'] );
                $matching_question_ids = Question::get_matches_in_other_versions( $stripped_question['ID'] );
                foreach( $matching_question_ids as $question_id ) {
                    $this->sort_orders_to_update[] = array( $question_id, $stripped_question['_kwps_new_sort_order'] );
                }

                unset( $stripped_question['_kwps_new_sort_order'] );
            }

            $this->question_id = Question::save_post( $stripped_question, true );

            if( ! isset( $question['ID'] ) ) {
                $matching_question_group_ids = Question_Group::get_matches_in_other_versions( $this->question_group_id );
                foreach( $matching_question_group_ids as $matched_id ) {
                    $new_question = $stripped_question;
                    $new_question['post_parent'] = $matched_id;
                    Question::save_post( $new_question, true);
                }
            }

            foreach( $question['answer_options'] as $answer_option_key => $answer_option ) {
                $this->answer_option_key = $answer_option_key;
                $this->save_answer_option_of_existing_version( $answer_option );
            }
        }
    }

    private function save_answer_option_of_existing_version( $answer_option ) {
        if( 'trash' == $answer_option['post_status'] ) {
            if( isset( $answer_option['ID'] ) ) {
                Answer_Option::set_matching_to_trash( $answer_option['ID'] );
                wp_delete_post( $answer_option['ID'], true );
            }
        } else {
            $answer_option['post_parent'] = $this->question_id;

            if( isset( $answer_option['_kwps_new_sort_order'] ) ) {
                $this->sort_orders_to_update[] = array( $answer_option['ID'], $answer_option['_kwps_new_sort_order'] );
                $matching_answer_option_ids = Answer_Option::get_matches_in_other_versions( $answer_option['ID'] );
                foreach( $matching_answer_option_ids as $answer_option_id ) {
                    $this->sort_orders_to_update[] = array( $answer_option_id, $answer_option['_kwps_new_sort_order'] );
                }

                unset( $answer_option['_kwps_new_sort_order'] );
            }

            Answer_Option::save_post( $answer_option, true );

            if( ! isset($answer_option['ID'] ) ) {
                $matching_question_ids = Question::get_matches_in_other_versions( $this->question_id );
                foreach( $matching_question_ids as $matched_id ) {
                    $new_answer_option = $answer_option;
                    $new_answer_option['post_parent'] = $matched_id;
                    Answer_Option::save_post( $new_answer_option, true );
                }
            }
        }
    }

    private function update_kwps_sort_order_of_form( $data ) {
        if( isset( $data['result_profiles'] ) ) {
            $data['result_profiles'] = $this->update_kwps_sort_order( $data['result_profiles'] );
        }

        $data['question_groups'] = $this->update_kwps_sort_order( $data['question_groups'] );

        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            $data['question_groups'][$question_group_key]['questions'] =
                $this->update_kwps_sort_order($data['question_groups'][$question_group_key]['questions']);
        }

        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
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

        $test_modus_errors = $this->validate_for_test_modus($data);

        if( isset( $data['post_status'] ) && 'publish' == $data['post_status'] ) {
            $test_modus_errors['post_status'] = __( 'Can not update a test that is published' );
        }

        if( Test_Collection::is_being_edited_by_other_user( $data['post_parent'] ) ) {
            $test_modus_errors['lock'] = __( 'Can not update a test while another user is working on it' );
        }

        if( sizeof( $test_modus_errors ) != 0 ) {
            $data_has_errors = true;
        }

        $stripped_version = array_diff_key( $data, array('question_groups' => '') );

        $test_modus = Test_Collection::get_test_modus( $stripped_version['post_parent'], false );
        $answer_options_require_value = ($test_modus['_kwps_answer_options_require_value'] > 0);

        $version_errors = Version::validate_for_update($stripped_version);

        $data['errors'] = $version_errors;

        if( sizeof( $version_errors ) != 0 ) {
            $data_has_errors = true;
        }

        if( isset( $data['intro']['ID'] ) ) {
            $intro_errors = Intro::validate_for_update( $data['intro'] );
        } else {
            $intro_errors = Intro::validate_for_insert( $data['intro'] );
        }
        $data['intro']['errors'] = $intro_errors;

        if( sizeof($intro_errors) != 0 ) {
            $data_has_errors = true;
        }

        if( isset( $data['intro_result']['ID'] ) ) {
            $intro_result_errors = Intro_Result::validate_for_update( $data['intro_result'] );
        } else {
            $intro_result_errors = Intro_Result::validate_for_insert( $data['intro_result'] );
        }
        $data['intro_result']['errors'] = $intro_result_errors;

        if( sizeof($intro_result_errors) != 0 ) {
            $data_has_errors = true;
        }

        if( isset( $data['outro']['ID'] ) ) {
            $outro_errors = Outro::validate_for_update( $data['outro'] );
        } else {
            $outro_errors = Outro::validate_for_insert( $data['outro'] );
        }

        if( ! Outro::has_valid_result_code_in_post_content( $data['outro'], $test_modus ) ) {
            if( ! isset( $outro_errors['post_content']) ) {
                $outro_errors['post_content'] = 'No valid result code used';
            }
        }

        $data['outro']['errors'] = $outro_errors;

        if( sizeof($outro_errors) != 0 ) {
            $data_has_errors = true;
        }

        if( $answer_options_require_value ) {
            $set_trashed_result_profiles_to_draft = false;

            $trashed_result_profiles_count = $this->get_trashed_items_count( $data['result_profiles'] );

            if( ( sizeof( $data['result_profiles'] ) - $trashed_result_profiles_count ) < 2 ) {
                $data_has_errors = true;
                $test_modus_errors['_kwps_min_result_profiles_per_version'] =
                    'Minimum 2 result profiles required per version';
                $set_trashed_result_profiles_to_draft = true;
            }

            foreach( $data['result_profiles'] as $result_profile_key => $result_profile ) {
                if( isset( $result_profile['ID'] ) ) {
                    $result_profile_errors = Result_Profile::validate_for_update( $result_profile );
                } else {
                    $result_profile_errors = Result_Profile::validate_for_insert( $result_profile );
                }

                if( $set_trashed_result_profiles_to_draft && $result_profile['post_status'] == 'trash' ) {
                    $result_profile_errors['post_status'] = 'Minimum 2 result profiles required per version';
                    $result_profile['post_status'] = 'draft';
                    $data['result_profiles'][$result_profile_key]['post_status'] = 'draft';
                }

                $data['result_profiles'][$result_profile_key]['errors'] = $result_profile_errors;
            }
        }

        $set_trashed_question_groups_to_draft = false;

        $trashed_question_groups_count = $this->get_trashed_items_count( $data['question_groups'] );

        if( ( sizeof( $data['question_groups'] ) - $trashed_question_groups_count ) < 1 ) {
            $data_has_errors = true;
            $test_modus_errors['_kwps_min_question_groups_per_version'] =
                'Minimum 1 question group required per version';
            $set_trashed_question_groups_to_draft = true;
        }

        foreach( $data['question_groups'] as $question_group_key => $question_group ) {
            $stripped_question_group = array_diff_key($question_group, array( 'questions' => '' ) );
            if( isset( $stripped_question_group['ID'] ) ) {
                $question_group_errors = Question_Group::validate_for_update( $stripped_question_group );
            } else {
                $question_group_errors = Question_Group::validate_for_insert( $stripped_question_group );

                if( isset( $test_modus_errors['_kwps_max_question_groups'] ) ) {
                    $question_group_errors['_kwps_max_question_groups'] = $test_modus_errors['_kwps_max_question_groups'];
                }
            }

            if( $set_trashed_question_groups_to_draft && $question_group['post_status'] == 'trash' ) {
                $question_group_errors['post_status'] = 'Minimum 1 question group required per version';
                $question_group['post_status'] = 'draft';
                $data['question_groups'][$question_group_key]['post_status'] = 'draft';
            }

            $data['question_groups'][$question_group_key]['errors'] = $question_group_errors;

            if( sizeof( $question_group_errors ) != 0 ) {
                $data_has_errors = true;
            }

            $set_trashed_questions_to_draft = false;

            $trashed_questions_count = $this->get_trashed_items_count( $question_group['questions'] );

            if( ( sizeof( $question_group['questions'] ) - $trashed_questions_count ) < 1 ) {
                $data_has_errors = true;
                $test_modus_errors['_kwps_min_questions_per_question_group'] =
                    'Minimum 1 question required per question group';
                $set_trashed_questions_to_draft = true;
            }

            foreach( $question_group['questions'] as $question_key => $question ) {
                $stripped_question = array_diff_key($question, array( 'answer_options' => '' ) );

                if( isset( $stripped_question['ID'] ) ) {
                    $question_errors = Question::validate_for_update($stripped_question);
                } else {
                    $question_errors = Question::validate_for_insert($stripped_question);

                    if( isset( $test_modus_errors['_kwps_max_questions_per_question_group'] ) ) {
                        $question_errors['_kwps_max_questions_per_question_group'] =
                            $test_modus_errors['_kwps_max_questions_per_question_group'];
                    }
                }

                if( $set_trashed_questions_to_draft && $question['post_status'] == 'trash' ) {
                    $question_errors['post_status'] = 'Minimum 1 question required per question group';
                    $question['post_status'] = 'draft';
                    $data['question_groups'][$question_group_key]['questions'][$question_key]['post_status'] = 'draft';
                }

                $data['question_groups'][$question_group_key]['questions'][$question_key]['errors'] = $question_errors;

                if( sizeof( $question_errors ) != 0 ) {
                    $data_has_errors = true;
                }

                $set_trashed_answer_options_to_draft = false;

                $trashed_answer_options_count = $this->get_trashed_items_count( $question['answer_options'] );

                if( ( sizeof( $question['answer_options'] ) - $trashed_answer_options_count ) < 2 ) {
                    $data_has_errors = true;
                    $test_modus_errors['_kwps_min_answer_options_per_question'] =
                        'Minimum 2 answer options required per question';
                    $set_trashed_answer_options_to_draft = true;
                }

                foreach( $question['answer_options'] as $answer_option_key => $answer_option ) {
                    if( isset( $answer_option['ID'] ) ) {
                        $answer_option_errors = Answer_Option::validate_for_update($answer_option);
                    } else {
                        $answer_option_errors = Answer_Option::validate_for_insert($answer_option);
                    }

                    if( $answer_options_require_value ) {
                        if(! isset($answer_option['_kwps_answer_option_value'])) {
                            $answer_option_errors['_kwps_answer_option_value'] = 'Required';
                        } else {
                            if( is_string($answer_option['_kwps_answer_option_value'])){
                                if( strlen($answer_option['_kwps_answer_option_value']) == 0 ) {
                                    $answer_option_errors['_kwps_answer_option_value'] = 'Required';
                                }
                            }
                        }

                        if( isset( $answer_option['_kwps_answer_option_value']) ) {
                            if(! is_numeric( $answer_option['_kwps_answer_option_value'] ) ){
                                $answer_option_errors['_kwps_answer_option_value'] = 'Needs to be a number';
                            }
                        }
                    }

                    if( $set_trashed_answer_options_to_draft && $answer_option['post_status'] == 'trash' ) {
                        $answer_option_errors['post_status'] = 'Minimum 2 answer options required per question';
                        $answer_option['post_status'] = 'draft';
                        $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'][$answer_option_key]['post_status'] = 'draft';
                    }

                    $data['question_groups'][$question_group_key]['questions'][$question_key]['answer_options'][$answer_option_key]['errors'] = $answer_option_errors;

                    if( sizeof( $answer_option_errors ) != 0 ) {
                        $data_has_errors = true;
                    }
                }


            }
        }

        return array( 'errors' => $data_has_errors, 'test_modus_errors' => $test_modus_errors, 'data' => $data );
    }

    private function get_trashed_items_count( $data ) {
        $trashed_items_count = 0;
        foreach( $data as $item ) {
            if( $item['post_status'] == 'trash' ) {
                $trashed_items_count++;
            }
        }

        return $trashed_items_count;
    }
} 