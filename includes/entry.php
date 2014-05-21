<?php
namespace includes;

require_once 'kwps_post_type.php';
require_once 'entry.php';

class Entry extends Kwps_Post_Type{

    public static $post_type = 'kwps_entry';

    public static $rewrite = array(
            'slug' => 'entries',
            'with_front' => false,
        );

    public static $post_type_args = array(
        'public' => false,
        'supports' => false,
        'labels' => array(
            'name' => 'Entries',
            'singular_name' => 'Entry',
            'add_new' => 'Add New Entry',
            'add_new_item' => 'Add New Entry',
            'edit_item' => 'Edit Entry',
            'new_item' => 'New Entry',
            'view_item' => 'View Entry',
            'search_items' => 'Search Entries',
            'not_found' => 'No Entrys Found',
            'not_found_in_trash' => 'No Entrys Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    public static function get_test_modus($entry_id)
    {
        $entry = static::get_as_array($entry_id);
        return Answer_Option::get_test_modus($entry['post_parent']);
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
        $request_data['_kwps_cookie_value'] = $_COOKIE['klasse_wp_poll_survey'];
        $request_data['_kwps_ip_address'] = Uniqueness::get_ip_of_user();
        $request_data['post_author'] = get_current_user_id();

        if( static::validate_for_insert($request_data) ) {
            static::save_post($request_data);
            wp_send_json( static::get_results_by_question( wp_get_post_parent_id( $request_data['post_parent'] )));
        } else {
            wp_send_json(null);
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
        $totalEntries = 0;

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

        $content_post = get_post($question_id);
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);
        
        $poll_question = $content;
        // var_dump($results);
        array_push($results, array( 'total_entries' => $totalEntries));
        array_push($results, array( 'poll_question' => $poll_question));
        return $results;
    }


    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_insert($entry = array()) {
        if( ! isset($entry['post_parent']) ){
            return false;
        }

        $answer_option = Answer_Option::get_as_array($entry['post_parent']);
        $question = Question::get_as_array($answer_option['post_parent']);
        $version = Version::get_as_array($question['post_parent']);

        $limitations = Test_Collection::get_meta_data($version['post_parent']);

        if( is_user_logged_in() ){
            return Uniqueness::is_allowed($question['ID'], $limitations['_kwps_logged_in_user_limit']);
        } else {
            return Uniqueness::is_allowed($question['ID'], $limitations['_kwps_logged_out_user_limit']);

        }
    }
}

/* EOF */