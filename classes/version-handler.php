<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 23/10/14
 * Time: 08:58
 */

namespace kwps_classes;


class Version_Handler {

    public function save_new_version_form($data){
        $version = array(
            'post_title' => $data['post_title'],
            'post_parent' => $data['post_parent'],
        );
        $version_id = Version::save_post($version, true);

        foreach( $data['question_groups'] as $question_group ) {
            $this->save_question_group( $version_id, $question_group);
        }

        return $version_id;
    }

    private function save_question_group( $version_id, $question_group ) {
        $data = array(
            'post_title' => $question_group['post_title'],
            'post_content' => $question_group['post_content'],
            '_kwps_sort_order' => $question_group['_kwps_sort_order'],
            'post_parent' => $version_id,
        );
        $question_group_id = Question_Group::save_post($data, true);

        foreach( $question_group['questions'] as $question ) {
            $this->save_question( $question_group_id, $question);
        }
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