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

    public static function get_test_modus($version_id)
    {
        $version = static::get_as_array($version_id);
        return Test_Collection::get_test_modus($version['post_parent']);
    }

    static function validate_for_insert($post_as_array = array()) {
        $errors = array();

        $numeric_fields = array(
            '_kwps_sort_order',
        );

        $required_fields = array(
            'post_status',
            '_kwps_sort_order',
        );

        foreach($required_fields as $field){
            if(! isset($post_as_array[$field])) {
                array_push($errors, array( $field, 'Required') );
            } else {
                if( is_string($post_as_array[$field])){
                    if( strlen($post_as_array[$field]) == 0 ) {
                        array_push($errors, array( $field, 'Required') );
                    }
                }
            }
        }

        foreach($numeric_fields as $field){
            if( isset( $post_as_array[$field]) ) {
                if(! is_numeric( $post_as_array[$field] ) ){
                    array_push( $errors , array( $field, 'Needs to be a number') );
                }
            }
        }

        return $errors;
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
        return true;
    }


    public static function validate_for_update($post_as_array){
        $post = get_post($post_as_array['ID'], ARRAY_A);

        if(null != $post){
            return true;    
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
	    $version = Version::get_as_array($id);
	    $limitations = Test_Collection::get_meta_data($version['post_parent']);

	    if( is_user_logged_in() ){
		    $limit_to_apply = $limitations['_kwps_logged_in_user_limit'];
	    } else {
		    $limit_to_apply = $limitations['_kwps_logged_out_user_limit'];
	    }


	    $data = array(
		    'settings' => array(
			    'first_question_id_allowed' => -1
		    )
	    );
	    $data['intro'] = Intro::get_one_by_post_parent($id);
	    $data['outro'] = Outro::get_one_by_post_parent($id);
	    $data['question_groups'] = Question_Group::get_all_by_post_parent($id);

	    foreach($data['question_groups'] as $questionGroupKey => $questionGroup) {
		    $data['question_groups'][$questionGroupKey]['questions'] = Question::get_all_by_post_parent($questionGroup['ID']);

		    foreach($data['question_groups'][$questionGroupKey]['questions'] as $questionKey => $question) {
			    if( Uniqueness::is_allowed($question['ID'], $limit_to_apply) && $data['settings']['first_question_id_allowed'] < 0 ){
				    $data['settings']['first_question_id_allowed'] = $question['ID'];
			    }
			    $data['question_groups'][$questionGroupKey]['questions'][$questionKey]['answer_options'] = Answer_Option::get_all_by_post_parent($question['ID']);
		    }
	    }

	    ob_start();
	    include_once(dirname(__FILE__) . '/../views/public/version.php');
		return ob_get_clean();
    }

}