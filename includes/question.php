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

        return '<div class="kwps-question">' . $question['post_content'] . '</div>';
    }
}