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

    public static $allowed_dropdown_values = array(
        '_kwps_logged_in_user_limit' => array(
            'free',
            'cookie',
            'ip',
            'once',
        ),
        '_kwps_logged_out_user_limit' => array(
            'free',
            'cookie',
            'ip',
            'none',
        ),
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

    public static function get_meta_data($test_collection_id){
        return array(
            '_kwps_logged_in_user_limit' => get_post_meta($test_collection_id, '_kwps_logged_in_user_limit', true),
            '_kwps_logged_out_user_limit' => get_post_meta($test_collection_id, '_kwps_logged_out_user_limit', true),
            '_kwps_sort_order' => get_post_meta($test_collection_id, '_kwps_sort_order', true),
        );
    }

    public static function get_test_modus($test_collection_id)
    {
        $test_collection = static::get_as_array($test_collection_id);
        return Test_Modus::get_as_array($test_collection['post_parent']);
    }


    static function validate_for_insert($post_as_array = array()) {
        $errors = array();

        $numeric_fields = array(
//            '_kwps_sort_order'
        );

        $required_fields = array(
            'post_status',
            'post_parent',
            '_kwps_sort_order',
            '_kwps_logged_in_user_limit',
            '_kwps_logged_out_user_limit',
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

        foreach( static::$allowed_dropdown_values as $field => $allowed_values ){
            if( isset( $post_as_array[$field] ) ) {
                if( !in_array( $post_as_array[$field], $allowed_values) ) {
                    array_push( $errors , array( $field, 'Value is not allowed') );
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
    }

    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
    }
}