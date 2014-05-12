<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 11/05/14
 * Time: 17:48
 */

namespace includes;

require_once 'kwps_post_type.php';


class Test_Collection extends Kwps_Post_Type{

    public static $post_type = 'kwps_test_collection';

    public static $rewrite = array(
        'slug' => 'test_collections',
        'with_front' => false,
    );

    public static $post_type_args = array(
        'public' => true,
        'supports' => array(
            'title',
        ),
        'labels' => array(
            'name' => 'Test Collections',
            'singular_name' => 'Test Collection',
            'add_new' => 'Add New Test Collection',
            'add_new_item' => 'Add New Test Collection',
            'edit_item' => 'Edit Test Collection',
            'new_item' => 'New Test Collection',
            'view_item' => 'View Test Collection',
            'search_items' => 'Search Test Collections',
            'not_found' => 'No Test Collections Found',
            'not_found_in_trash' => 'No Test Collections Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
    );

//    public static function save_post($post_data){
//        if( !isset($post_data['_kwps_uid']) ){
//
//        }
//        $post_id = wp_insert_post($post_data);
//
//        $post = get_post($post_id);
//
//        if( $post_id != 0 ){
//            foreach($post_data as $field => $value){
//                if( strpos($field, 'kwps') ) {
//                    update_post_meta($post_id, $field, $value);
//                }
//            }
//        } else {
//            return null;
//        }
//
//        return $post;
//    }

    public static function validate_for_insert($post_as_array = array())
    {
        return true;
    }

    public static function validate_for_update($post_as_array)
    {
        // TODO: Implement validate_for_update() method.
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
    }

    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
    }
}