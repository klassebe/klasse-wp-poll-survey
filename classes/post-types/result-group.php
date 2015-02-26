<?php

namespace kwps_classes;

require_once 'kwps-post-type.php';

class Result_Group extends Kwps_Post_Type{

    public static $required_fields = array(
        'post_title',
        'post_parent',
        '_kwps_referer',
    );

    public static $form_fields = array(
        'ID',
        'post_title',
        'post_parent',
        '_kwps_hash',
        '_kwps_result_hash',
        '_kwps_referer',
    );

    public static $numeric_fields = array(
    );

    public static $meta_data_fields = array(
        '_kwps_hash',
        '_kwps_result_hash',
        '_kwps_referer',
    );

    public static $additional_validation_methods = array();

    public static $label = 'kwps-result-group';
    public static $rewrite = array(
            'slug' => 'resultgroups',
            'with_front' => false,
        );

    public static $post_type = 'kwps_result_group';

    public static $post_type_args = array(
        'public' => true,
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
        'publicly_queryable' => true,
    );

    public static function get_test_modus($result_group_id)
    {
        $result_group = static::get_as_array($result_group_id);
        return Test_Collection::get_test_modus($result_group['post_parent']);
    }

    public static function get_by_result_hash($result_hash) {
        $args = array(
            'post_type' => static::$post_type,
            'meta_query' => array(
                array(
                    'key' => '_kwps_result_hash',
                    'value' => $result_hash,
                ),
            ),
            'post_status' => array( 'draft', 'publish'),
        );
        $result_groups = get_posts( $args );

        if( sizeof( $result_groups ) > 0 ) {
            return static::get_as_array( $result_groups[0]->ID );
        } else {
            return false;
        }

    }

    public static function is_valid_hash($test_collection_id, $hash){
        $result_groups = static::get_all_by_post_parent($test_collection_id);

        foreach( $result_groups as $result_group ) {
            if( $hash == $result_group['_kwps_hash'] ) {
                return true;
            }
        }

        return false;
    }

    public static function get_html($result_group_id){
        return '';
    }

    public static function validate_for_delete($result_group_id = 0)
    {
        return false;        
    }
}