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


    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
        return '';
    }


    public static function validate_for_insert($post_as_array = array())
    {
        // TODO: Implement validate_for_insert() method.
        return true;
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