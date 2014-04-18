<?php

namespace includes;

require_once 'kwps_post_type.php';

class Answer_Option extends Kwps_Post_Type{

    public static $post_type = 'kwps_answer_option';

    public static $post_type_args = array(
        'public' => false,
        'rewrite' => array(
            'slug' => 'answer_options',
            'with_front' => false,
        ),
        'supports' => false,
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    public static function get_meta_data($post_id){

    }

    public static function get_all_html($question_id)
    {
        $i = 0;
        $dump = '';
        foreach(static::get_all($question_id) as $answer_option){
            $dump .= '<div class="kwps-single-answer kwps-answer-' . $i++ . '"><input type="radio" name="kwps-answer-';
            $dump .= $answer_option['post_parent'] .'" value="'. $answer_option['ID'] .'">'. $answer_option['post_content'] . '</div>               ';
        }

        return $dump;
    }
}