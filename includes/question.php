<?php
namespace includes;

require_once 'kwps_post_type.php';

class Question extends Kwps_Post_Type{

    public static $post_type = 'kwps_question';

    public static $post_type_args = array(
        'public' => false,
        'rewrite' => array(
            'slug' => 'questions',
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
        return array('test question');
    }

    public static function get_html($question_id){
        $question = static::get_as_array($question_id);

        return '<div class="kwps-question">' . $question['post_title'] . '</div>';
    }

    public static function delete_meta()
    {
        // TODO: Implement delete_meta() method.
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
        return true;
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