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
        return array();
    }

    public static function get_all_html($question_id)
    {
        $i = 0;
        $dump = '';
        foreach(static::get_all($question_id) as $answer_option){
            $dump .= '<div class="kwps-single-answer kwps-answer-' . $i++ . '">';
            $dump .= '<input type="radio" name="kwps-answer"';
            $dump .= $answer_option['post_parent'];
            $dump .= ' value="'. $answer_option['ID'] .'">'. $answer_option['post_title'] . '</div>';
        }

        return $dump;
    }

    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_insert($post_as_array = array()) {
        $required_fields = array(
            'post_title',
            'post_status',
            'post_type',
            'post_parent'
        );

        foreach($required_fields as $field)
            if(! isset($post_as_array[$field])) {
                return false;
            } else {
                if( is_string($post_as_array[$field])){
                    if( strlen($post_as_array[$field]) == 0 ) {
                        return false;
                    }
                }
            }
        return true;
    }

    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_update($post_as_array = array()) {
        $required_fields = array(
            'ID',
            'post_title',
            'post_status',
            'post_type',
            'post_parent'
        );

        foreach($required_fields as $field)
            if(! isset($post_as_array[$field])) {
                return false;
            } else {
                if( is_string($post_as_array[$field])){
                    if( strlen($post_as_array[$field]) == 0 ) {
                        return false;
                    }
                }
            }
        return true;
    }
}