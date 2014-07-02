<?php

namespace includes;

require_once 'kwps-post-type.php';

class Intro extends Kwps_Post_Type{

    public static $required_fields = array(
        'post_content',
        'post_status',
        'post_parent',
        '_kwps_sort_order',
    );

    public static $numeric_fields = array(
        '_kwps_sort_order',
    );

    public static $additional_validation_methods = array();

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
}