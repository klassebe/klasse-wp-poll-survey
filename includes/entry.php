<?php
namespace includes;

require_once 'kwps_post_type.php';

class Entry extends Kwps_Post_Type{

    public static $post_type = 'kwps_entry';

    public static $rewrite = array(
            'slug' => 'entries',
            'with_front' => false,
        );

    public static $post_type_args = array(
        'public' => false,
        'supports' => false,
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    public static function get_meta_data($post_id){
        return array('test entry');
    }

    public static function get_html($entry_id){
        $entry = static::get_as_array($entry_id);

        return '<div class="kwps-entry">' . $entry['post_content'] . '</div>';
    }

    public static function validate_for_update($post_as_array)
    {
        // TODO: Implement validate_for_update() method.
        return true;
    }

    public static function validate_for_delete($entry_id = 0){
        $answer_option_id = wp_get_post_parent_id($entry_id);
        return Answer_Option::validate_for_delete($answer_option_id);
    }

    public static function save_from_request(){


        $request_data = static::get_post_data_from_request();
        if( static::validate_for_insert($request_data) ) {
            static::save_post($request_data);
            wp_send_json( static::get_results_by_question( wp_get_post_parent_id( $request_data['post_parent'] )));
        } else {
            return wp_send_json(null);
        }

        die();
    }

    public static function get_results_by_question($question_id){
        $args = array(
            'post_parent' => $question_id,
            'post_type'   => 'kwps_answer_option', 
            'posts_per_page' => -1,
            'post_status' => 'any',
        );

        $answer_options = get_children($args, ARRAY_A);

        $results = array('entries' => array());

        foreach ($answer_options as $answer_option) {
            $args = array(
            'post_parent' => $answer_option['ID'],
            'post_type'   => 'kwps_entry', 
            'posts_per_page' => -1,
            'post_status' => 'any',
            );

            $entries = get_children($args, ARRAY_A);
            // var_dump($answer_option['ID']);
            // var_dump($entries);
            array_push($results['entries'], array('answer_option_id' => $answer_option['ID'], 
                'answer_option_content' => $answer_option['post_content'],'entry_count' => count($entries)));
            $totalEntries += count($entries);
        }

        // var_dump($results);
        array_push($results, array( 'total_entries' => $totalEntries));
        return $results;
    }


    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_insert($post_as_array = array()) {
        // $required_fields = array(
        //     'post_parent'
        // );

        // foreach($required_fields as $field)
        //     if(! isset($post_as_array[$field])) {
        //         return false;
        //     } else {
        //         if( is_string($post_as_array[$field])){
        //             if( strlen($post_as_array[$field]) == 0 ) {
        //                 return false;
        //             }
        //         }
        //     }
        return true;
    }
}

/* EOF */