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

        $errors = static::validate_for_insert($request_data);
        if( sizeof( $errors ) == 0 ) {
            $post = static::save_post($request_data);
            wp_send_json( $post );
        } else {
            wp_send_json(null);
        }

        die();
    }

    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_insert($post_as_array = array()) {
        $errors = array();

        $numeric_fields = array(
        );

        $required_fields = array(
            'post_parent',
        );

        foreach($required_fields as $field){
            if(! isset($post_as_array[$field])) {
                array_push($errors, array( $field, 'Required') );
            } else {
                if( is_string($post_as_array[$field])){
                    if( strlen($post_as_array[$field]) == 0 ) {
                        array_push($errors, array( $field, 'Required') );
                    }
                }
            }
        }

        foreach($numeric_fields as $field){
            if( isset( $post_as_array[$field]) ) {
                if(! is_numeric( $post_as_array[$field] ) ){
                    array_push( $errors , array( $field, 'Needs to be a number') );
                }
            }
        }

        if( isset( $post_as_array['post_parent'] ) ){
            $answer_option = Answer_Option::get_as_array($post_as_array['post_parent']);
            $question = Question::get_as_array($answer_option['post_parent']);
            $question_group = Question_Group::get_as_array($question['post_parent']);
            $version = Version::get_as_array($question_group['post_parent']);

            $limitations = Test_Collection::get_meta_data($version['post_parent']);

            if( is_user_logged_in() ){
                if( ! Uniqueness::is_allowed($question['ID'], $limitations['_kwps_logged_in_user_limit']) ){
                    array_push( $errors, array('All', 'You have the reached limit to participate') );
                }
            } else {
                if( ! Uniqueness::is_allowed($question['ID'], $limitations['_kwps_logged_out_user_limit']) ){
                    array_push( $errors, array('All', 'You have reached the limit to participate') );
                }
            }
        }

        return $errors;
    }
}

/* EOF */