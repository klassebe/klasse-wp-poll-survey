<?php

namespace includes;

require_once 'kwps_post_type.php';

class Result_Group extends Kwps_Post_Type{

    public static $required_fields = array(
        'post_title',
        'post_parent',
    );

    public static $numeric_fields = array(
    );

    public static $meta_data_fields = array(
        '_kwps_group',
    );

    public static $additional_validation_methods = array();

    public static $label = 'kwps-result-group';
    public static $rewrite = array(
            'slug' => 'resultgroups',
            'with_front' => false,
        );

    public static $post_type = 'kwps_result_group';

    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Result Groups',
            'singular_name' => 'Result Group',
            'add_new' => 'Add New Result Group',
            'add_new_item' => 'Add New Result Group',
            'edit_item' => 'Edit Result Group',
            'new_item' => 'New Result Group',
            'view_item' => 'View Result Group',
            'search_items' => 'Search Result Groups',
            'not_found' => 'No Result Groups Found',
            'not_found_in_trash' => 'No Result Groups Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    public static function get_test_modus($result_group_id)
    {
        $result_group = static::get_as_array($result_group_id);
        return Test_Collection::get_test_modus($result_group['post_parent']);
    }

    public static function get_html($intro_id){
        return '';
    }

    public static function validate_for_delete($intro_id = 0)
    {
        return false;        
    }
}