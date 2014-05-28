<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 13/05/14
 * Time: 11:32
 */

namespace includes;


class Question_Group extends Kwps_Post_Type {

    public static $numeric_fields = array();

    public static $required_fields = array(
        'post_title',
        'post_parent',
        '_kwps_sort_order',
    );

    public static $meta_data_fields = array('_kwps_sort_order');

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

    public static function get_version($question_group_id){
        $question_group = static::get_as_array($question_group_id);
        return Version::get_as_array($question_group['post_parent']);
    }


    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
        return '';
    }


    public static function validate_for_insert($post_as_array = array())
    {
        $errors = static::check_required_fields($post_as_array);
        $errors = array_merge($errors, static::check_numeric_fields($post_as_array));
        $errors = array_merge($errors, static::check_max_question_groups($post_as_array));

        return $errors;
    }

    private static function check_max_question_groups($post){
        $errors = array();

        if( isset( $post['post_parent'] ) ){
            $version = Version::get_as_array($post['post_parent']);
            $test_modus = Version::get_test_modus($version['ID']);

            $kwps_max_question_groups = $test_modus['_kwps_max_question_groups'];

            if( 0 < $kwps_max_question_groups ){
                $all_question_groups_of_version = Question_Group::get_all_by_post_parent($version['ID']);

                if( sizeof($all_question_groups_of_version) >= $kwps_max_question_groups){
                    array_push( $errors, array( 'field' => 'All', 'message' =>'Maximum question groups already reached' ) );
                }
            }
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