<?php

namespace includes;

require_once 'kwps_post_type.php';

class Intro extends Kwps_Post_Type{
    public static $label = 'kwps-intro';
    public static $rewrite = array(
            'slug' => 'intros',
            'with_front' => false,
        );

    public static $post_type = 'kwps_intro';

    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Intros',
            'singular_name' => 'Intro',
            'add_new' => 'Add New Intro',
            'add_new_item' => 'Add New Intro',
            'edit_item' => 'Edit Intro',
            'new_item' => 'New Intro',
            'view_item' => 'View Intro',
            'search_items' => 'Search Intros',
            'not_found' => 'No Intros Found',
            'not_found_in_trash' => 'No Intros Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    public static function get_sort_order($intro_id){
        return get_post_meta($intro_id, '_kwps_sort_order', true);
    }

    public static function get_test_modus($intro_id)
    {
        $intro = static::get_as_array($intro_id);
        return Version::get_test_modus($intro['post_parent']);
    }

    public static function get_html($intro_id){
        $intro = static::get_as_array($intro_id);

        $dump = '<div class="' . static::$label . wp_get_post_parent_id($intro_id) . '">';
        $dump .= $intro['post_content'] . '</div>';

        return $dump;
    }

    public static function validate_for_delete($intro_id = 0)
    {
        return false;        
    }

    static function validate_for_insert($post_as_array = array()) {
        $errors = array();

        $numeric_fields = array(
            '_kwps_sort_order',
        );

        $required_fields = array(
            'post_content',
            'post_status',
            'post_parent',
            '_kwps_sort_order',
        );

        foreach($required_fields as $field){
            if(! isset($post_as_array[$field])) {
                array_push($errors, array( $field, 'Required') );
            } else {
                if( is_string($post_as_array[$field])){
                    if( strlen($post_as_array[$field]) == 0 ) {
                        array_push($errors, array( 'field' => $field, 'message' => 'Required') );
                    }
                }
            }
        }

        foreach($numeric_fields as $field){
            if( isset( $post_as_array[$field]) ) {
                if(! is_numeric( $post_as_array[$field] ) ){
                    array_push( $errors , array( 'field' => $field, 'message' => 'Needs to be a number') );
                }
            }
        }

        return $errors;
    }

    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_update($post_as_array = array()) {
        $required_fields = array(
            'ID',
            'post_content',
            'post_status',
            'post_parent'
        );

        foreach($required_fields as $field)
            if(! isset($post_as_array[$field])) {
                return false;
            } else {
                if( is_string($post_as_array[$field])){
                    if( strlen($post_as_array[$field]) == 0 ) {
                        return false;
                    }
                }
            }
        return true;
    }
}