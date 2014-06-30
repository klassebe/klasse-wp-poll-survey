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

    public static $numeric_fields = array(
        '_kwps_sort_order'
    );

    public static $required_fields = array(
            'post_status',
            'post_parent',
            '_kwps_sort_order',
            '_kwps_logged_in_user_limit',
            '_kwps_logged_out_user_limit',
        );

    public static $additional_validation_methods = array(
        'check_allowed_dropdown_values',
    );

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

    public static $meta_data_fields = array(
        '_kwps_logged_in_user_limit',
        '_kwps_logged_out_user_limit',
        '_kwps_sort_order',
        '_kwps_show_grouping_form',
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

    public static function get_test_modus($test_collection_id)
    {
        $test_collection = static::get_as_array($test_collection_id);
        return Test_Modus::get_as_array($test_collection['post_parent']);
    }

    public static function get_view_count($test_collection_id){
        $view_count_total = 0;
        $versions = Version::get_all_by_post_parent($test_collection_id);

        foreach($versions as $version){
            $view_count = (int) $version['_kwps_view_count'];
            $view_count_total = $view_count_total + $view_count;
        }

        return $view_count_total;
    }

    public static function check_allowed_dropdown_values($post){
        $errors = array();
        foreach( static::$allowed_dropdown_values as $field => $allowed_values ){
            if( isset( $post[$field] ) ) {
                if( !in_array( $post[$field], $allowed_values) ) {
                    array_push( $errors ,
                        array(
                            'field' => $field,
                            'message' => __( 'Value is not allowed', 'klasse-wp-poll-survey' ),
                        )
                    );
                }
            }
        }
        return $errors;
    }

    public static function ajax_validate_for_publish(){
        $test_collection = static::get_post_data_from_request();
        $response = static::validate_for_publish($test_collection);

        if( sizeof( $response ) > 0 ) {
            wp_send_json_error($response);
        } else {
            wp_send_json_success($response);
        }

        die();
    }

    public static function validate_for_publish($test_collection){
        $versions = Version::get_all_by_post_parent($test_collection['ID']);

        $errors = array();

        foreach($versions as $version){
            $version_errors = Version::validate_for_publish($version);
            if( sizeof( $version_errors) > 0) {
                $errors[] = $version_errors;
            }
        }

        if( sizeof( $errors ) != 0 ) {
            foreach($versions as $version){
                $version['post_status'] = 'publish';
                Version::save_post($version);
            }
        }

        return $errors;
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