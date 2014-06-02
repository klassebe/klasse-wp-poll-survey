<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 13/05/14
 * Time: 11:32
 */

namespace includes;


class Result_Profile extends Kwps_Post_Type {

    public static $numeric_fields = array(
	    '_kwps_min_value',
	    '_kwps_max_value'
    );

    public static $required_fields = array(
        'post_title',
        'post_parent',
        '_kwps_sort_order',
	    '_kwps_min_value',
	    '_kwps_max_value'
    );

    public static $meta_data_fields = array('_kwps_sort_order');

    public static $post_type = 'kwps_result_profile';

    public static $rewrite = array(
        'slug' => 'result_profiles',
        'with_front' => false,
    );

    public static $post_type_args = array(
        'public' => true,
        'supports' => array(
            'title',
        ),
        'labels' => array(
            'name' => 'Result Profiles',
            'singular_name' => 'Result Profile',
            'add_new' => 'Add New Result Profile',
            'add_new_item' => 'Add New Result Profile',
            'edit_item' => 'Edit Result Profile',
            'new_item' => 'New Result Profile',
            'view_item' => 'View Result Profile',
            'search_items' => 'Search Result Profiles',
            'not_found' => 'No Result Profiles Found',
            'not_found_in_trash' => 'No Result Profiles Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
    );

    public static function get_test_modus($result_profile_id)
    {
	    $result_profile = static::get_as_array($result_profile_id);
        return Version::get_test_modus($result_profile['post_parent']);
    }

    public static function get_version($result_profile_id){
	    $result_profile = static::get_as_array($result_profile_id);
        return Version::get_as_array($result_profile['post_parent']);
    }


    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
        return '';
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