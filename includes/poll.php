<?php

namespace includes;

require_once 'kwps_post_type.php';
require_once 'question.php';
require_once 'answer_option.php';

class Poll extends Kwps_Post_Type{

    public static $post_type = 'kwps_poll';

    public static $rewrite = array(
            'slug' => 'polls',
            'with_front' => false,
        );

    public static $post_type_args = array(
            'public' => true,
            'supports' => array(
                'title',
            ),
            'labels' => array(
                'name' => 'Polls',
                'singular_name' => 'Poll',
                'add_new' => 'Add New Poll',
                'add_new_item' => 'Add New Poll',
                'edit_item' => 'Edit Poll',
                'new_item' => 'New Poll',
                'view_item' => 'View Poll',
                'search_items' => 'Search Polls',
                'not_found' => 'No Polls Found',
                'not_found_in_trash' => 'No Polls Found In Trash',
            ),
            'show_in_menu' => false,
            'show_ui' => false,
            'hierarchical' => true,
    );

    public static function get_meta_data($post_id){
        $meta_as_array = array();
        $meta_as_array['_kwps_intro'] = get_post_meta($post_id, '_kwps_intro', true);
        $meta_as_array['_kwps_outro'] = get_post_meta($post_id, '_kwps_outro', true);
        $meta_as_array['_kwps_view_count'] = get_post_meta($post_id, '_kwps_view_count', true);
        return $meta_as_array;
    }

    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_insert($post_as_array = array()) {
        $required_fields = array(
            'post_status',
            'post_type',
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

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
        return true;
    }


    public static function validate_for_update($post_as_array){
        $post = get_post($post_as_array['ID'], ARRAY_A);

        if(null != $post){
            if($post['post_status'] == 'publish'){
                return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public static function shortcode($atts){
        extract( shortcode_atts( array(
            'id' => 0,
            'version' => 'all',
        ), $atts ) );

        return static::get_html($id);
    }

    public static function get_html($id){
        $dump = '';

        $post_as_array = static::get_as_array($id);

        if($test_exists = $post_as_array['post_status'] === 'publish'){
            $dump .= '<div class="' . get_post_type( $id ) . '" id="kwps-' . $id . '" >';
            // $dump .= '<input type="hidden">'
            $dump .= '<div class="kwps-title">' . get_the_title( $id ) . '</div>';
            $dump .= '<div class="kwps-intro"><p>' . get_post_meta( $id, '_kwps_intro', true) . '</p><input type="button" class="kwps-next" value="Volgende"></div>';
            $dump .= '<div class="kwps-content">';

            $questions = Question::get_all_children($id);
            $question = $questions[0];
            $dump .= '<div class="kwps-question">';
            $dump .= Question::get_html($question['ID']);
            $dump .= '</div>';
            $dump .= '<div class="kwps-answers">';

            $dump .= '<form id="form-poll-' . $id . '" class="form-poll" action="/">';
            $dump .= Answer_Option::get_all_html($question['ID']);
 
            $dump .= '</form>';
            $dump .= '</div>'; // kwps-answers
            $dump .= '</div>'; // kwps-content
            $dump .= '<div class="kwps-outro"><p>' . get_post_meta( $id, '_kwps_outro', true) . '</p><div class="kwps-outro-inside"></div></div>';
            $dump .= '</div>'; // kwps full wrapper
        } else {
            $dump .= "Poll kan niet getoond worden.";
        }

        return $dump;
    }

}