<?php

namespace kwps_classes;

require_once 'intro.php';

class Outro extends Intro{

    public static $label = 'kwps-outro';
    public static $rewrite = array(
            'slug' => 'outros',
            'with_front' => false,
        );

    public static $post_type = 'kwps_outro';

    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Outros',
            'singular_name' => 'Outro',
            'add_new' => 'Add New Outro',
            'add_new_item' => 'Add New Outro',
            'edit_item' => 'Edit Outro',
            'new_item' => 'New Outro',
            'view_item' => 'View Outro',
            'search_items' => 'Search Outros',
            'not_found' => 'No Outros Found',
            'not_found_in_trash' => 'No Outros Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );
}