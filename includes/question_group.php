<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 13/05/14
 * Time: 11:32
 */

namespace includes;


class Question_Group extends Kwps_Post_Type {

    public static $post_type = 'kwps_question_group';

    public static $rewrite = array(
        'slug' => 'question_groups',
        'with_front' => false,
    );

    public static $post_type_args = array(
        'public' => true,
        'supports' => array(
            'title',
        ),
        'labels' => array(
            'name' => 'Question Groups',
            'singular_name' => 'Question Group',
            'add_new' => 'Add New Question Group',
            'add_new_item' => 'Add New Question Group',
            'edit_item' => 'Edit Question Group',
            'new_item' => 'New Question Group',
            'view_item' => 'View Question Group',
            'search_items' => 'Search Question Groups',
            'not_found' => 'No Question Groups Found',
            'not_found_in_trash' => 'No Question Groups Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
    );

    public static function get_test_modus($question_group_id)
    {
        $question_group = static::get_as_array($question_group_id);
        return Version::get_test_modus($question_group['post_parent']);
    }


    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
        return '';
    }


    public static function validate_for_insert($post_as_array = array())
    {
        $errors = array();

        $numeric_fields = array(
            '_kwps_sort_order',
        );

        $required_fields = array(
            'post_title',
            'post_parent',
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

        $version = Version::get_as_array($post_as_array['post_parent']);
        $test_modus = Version::get_test_modus($version['ID']);

        $kwps_max_question_groups = $test_modus['_kwps_max_question_groups'];

        $all_question_groups_of_version = Question_Group::get_all_by_post_parent($version['ID']);

        if( sizeof($all_question_groups_of_version) >= $kwps_max_question_groups){
            array_push( $errors, array( 'All', 'Maximum question groups already reached' ) );
        }

        return $errors;
    }

    public static function validate_for_update($post_as_array)
    {
        // TODO: Implement validate_for_update() method.
        return true;
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
        return true;
    }


} 