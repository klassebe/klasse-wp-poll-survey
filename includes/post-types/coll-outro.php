<?php

namespace includes;

require_once 'intro.php';

class Coll_Outro extends Intro{
    public static $label = 'kwps-coll-outro';
    public static $rewrite = array(
            'slug' => 'coll-outro',
            'with_front' => false,
        );

    public static $post_type = 'kwps_coll_outro';

    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Test Collection Outros',
            'singular_name' => 'Test Collection Outro',
            'add_new' => 'Add New Test Collection Outro',
            'add_new_item' => 'Add New Test Collection Outro',
            'edit_item' => 'Edit Test Collection Outro',
            'new_item' => 'New Test Collection Outro',
            'view_item' => 'View Test Collection Outro',
            'search_items' => 'Search Test Collection Outros',
            'not_found' => 'No Test Collection Outros Found',
            'not_found_in_trash' => 'No Test Collection Outros Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );


}