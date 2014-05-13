<?php

namespace includes;

require_once 'kwps_post_type.php';

class Answer_Option extends Kwps_Post_Type{

    public static $post_type = 'kwps_answer_option';

    public static $rewrite = array(
            'slug' => 'answer_options',
            'with_front' => false,
        );

    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Answer Options',
            'singular_name' => 'Answer',
            'add_new' => 'Add New Answer',
            'add_new_item' => 'Add New Answer',
            'edit_item' => 'Edit Answer',
            'new_item' => 'New Answer',
            'view_item' => 'View Answer',
            'search_items' => 'Search Answers',
            'not_found' => 'No Answers Found',
            'not_found_in_trash' => 'No Answers Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    public static function get_sort_order($answer_option_id){
        return get_post_meta($answer_option_id, '_kwps_sort_order', true);
    }

    public static function get_html($answer_option_id){
        $answer_option = static::get_as_array($answer_option_id);

        $dump = '<div class="kwps-single-answer kwps-answer-' . static::get_sort_order($answer_option_id) . '">';
        $dump .= '<input type="radio" name="kwps-answer"';
        $dump .= $answer_option['post_parent'];
        $dump .= ' value="'. $answer_option['ID'] .'">'. $answer_option['post_content'] . '</div>';

        return $dump;
    }

    public static function validate_for_delete($answer_option_id = 0)
    {
        $question_id = wp_get_post_parent_id($answer_option_id);
        return Question::validate_for_delete($question_id);
    }


    public static function get_all_html($question_id)
    {
        $i = 0;
        $dump = '';
        foreach(static::get_all_by_post_parent($question_id) as $answer_option){
            $dump .= static::get_html($answer_option['ID']);
        }

        return $dump;
    }

    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_insert($post_as_array = array()) {
        $required_fields = array(
            'post_content',
            'post_status',
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
            'post_content',
            'post_status',
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