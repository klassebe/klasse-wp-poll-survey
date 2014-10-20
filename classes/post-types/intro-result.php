<?php

namespace kwps_classes;

require_once 'intro.php';

class Intro_Result extends Intro{
    public static $label = 'kwps-intro-result';
    public static $rewrite = array(
            'slug' => 'intro-results',
            'with_front' => false,
        );

    public static $post_type = 'kwps_intro_result';

    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Intros',
            'singular_name' => 'Intro Result',
            'add_new' => 'Add New Intro Result',
            'add_new_item' => 'Add New Intro Result',
            'edit_item' => 'Edit Intro Result',
            'new_item' => 'New Intro Result',
            'view_item' => 'View Intro Result',
            'search_items' => 'Search Intros Result',
            'not_found' => 'No Intro Results Found',
            'not_found_in_trash' => 'No Intros Result Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );


}