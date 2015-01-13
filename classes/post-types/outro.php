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

    public static function has_valid_result_code_in_post_content( $outro, $test_modus ) {
        $pattern = '/\[kwps_result.*\]/';
        $subject = $outro['post_content'];
        $shortcode_count = preg_match_all($pattern, $subject, $kwps_result_matches);


        if( $shortcode_count <= 0 ){
            return false;
        } else {
            foreach($kwps_result_matches as $shortcode){
                $output_start_pos = strpos($shortcode[0], '=');

                $output_type_temp = substr($shortcode[0], $output_start_pos + 1);
                $output_type = trim($output_type_temp, ']');

                if( !in_array( $output_type, $test_modus['_kwps_allowed_output_types'] ) ) {
                    return false;
                }
            }
        }
        return true;
    }
}