<?php

namespace includes;

require_once 'kwps_post_type.php';
require_once 'question.php';
require_once 'answer_option.php';

class Version extends Kwps_Post_Type{
    public static $label = 'kwps-version';

    public static $post_type = 'kwps_version';

    public static $rewrite = array(
            'slug' => 'versions',
            'with_front' => false,
        );

    public static $post_type_args = array(
            'public' => true,
            'supports' => array(
                'title',
            ),
            'labels' => array(
                'name' => 'versions',
                'singular_name' => 'version',
                'add_new' => 'Add New version',
                'add_new_item' => 'Add New version',
                'edit_item' => 'Edit version',
                'new_item' => 'New version',
                'view_item' => 'View version',
                'search_items' => 'Search versions',
                'not_found' => 'No versions Found',
                'not_found_in_trash' => 'No versions Found In Trash',
            ),
            'show_in_menu' => false,
            'show_ui' => false,
            'hierarchical' => true,
    );

    public static function get_meta_data($post_id){
        $meta_as_array = array();
        $meta_as_array['_kwps_sort_order'] = get_post_meta($post_id, '_kwps_sort_order', true);
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
        $test_collection = Test_Collection::get_as_array($post_as_array['post_parent']);
        $test_modus = Test_Modus::get_as_array($test_collection['post_parent']);
        $test_modus_name = $test_modus['post_title'];

        if($test_exists = $post_as_array['post_status'] === 'publish'){
            $intros = Intro::get_all_children($id);
            $intro = $intros[0];

            $dump .= '<div class="' . $test_modus_name . '" id="kwps-' . $id . '" >';
            // $dump .= '<input type="hidden">'
            $dump .= '<div class="kwps-title">' . get_the_title( $id ) . '</div>';
            $dump .= '<div class="kwps-intro">' . Intro::get_html($intro['ID']) . '<input type="button" class="kwps-next" value="Volgende"></div>';
            $dump .= '<div class="kwps-content">';

            $questions = Question::get_all_children($id);
            $question = $questions[0];
            $dump .= '<div class="kwps-question">';
            $dump .= Question::get_html($question['ID']);
            $dump .= '</div>';
            $dump .= '<div class="kwps-answers">';

            $dump .= '<form id="form-version-' . $id . '" class="form-version" action="/">';
            $dump .= Answer_Option::get_all_html($question['ID']);
 
            $dump .= '</form>';
            $dump .= '</div>'; // kwps-answers
            $dump .= '</div>'; // kwps-content

            $outros = Outro::get_all_children($id);
            $outro = $outros[0];
            $dump .= '<div class="kwps-outro">' . Outro::get_html($outro['ID']) . '<div class="kwps-outro-inside"></div></div>';
            $dump .= '</div>'; // kwps full wrapper
            $dump .= '<input type=hidden id=adminUrl value='. admin_url() .'>';
        } else {
            $dump .= "versie kan niet getoond worden.";
        }

        return $dump;
    }

}