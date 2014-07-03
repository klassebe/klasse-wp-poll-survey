<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 13/05/14
 * Time: 11:32
 */

namespace includes;


class Result_Profile extends Kwps_Post_Type {

    public static $numeric_fields = array(
	    '_kwps_sort_order',
	    '_kwps_min_value',
	    '_kwps_max_value'
    );

    public static $required_fields = array(
        'post_title',
        'post_parent',
        '_kwps_sort_order',
	    '_kwps_min_value',
	    '_kwps_max_value'
    );

    public static $meta_data_fields = array(
        '_kwps_sort_order',
        '_kwps_min_value',
        '_kwps_max_value',
    );

    public static $post_type = 'kwps_result_profile';

    public static $rewrite = array(
        'slug' => 'result_profiles',
        'with_front' => false,
    );

    public static $post_type_args = array(
        'public' => true,
        'supports' => array(
            'title',
        ),
        'labels' => array(
            'name' => 'Result Profiles',
            'singular_name' => 'Result Profile',
            'add_new' => 'Add New Result Profile',
            'add_new_item' => 'Add New Result Profile',
            'edit_item' => 'Edit Result Profile',
            'new_item' => 'New Result Profile',
            'view_item' => 'View Result Profile',
            'search_items' => 'Search Result Profiles',
            'not_found' => 'No Result Profiles Found',
            'not_found_in_trash' => 'No Result Profiles Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
    );

    public static function get_test_modus($result_profile_id)
    {
	    $result_profile = static::get_as_array($result_profile_id);
        return Version::get_test_modus($result_profile['post_parent']);
    }

    public static function get_version($result_profile_id){
	    $result_profile = static::get_as_array($result_profile_id);
        return Version::get_as_array($result_profile['post_parent']);
    }

    public static function ajax_get_by_entry_id(){
        $request_data = static::get_post_data_from_request();

        $errors = array();

        if( ! isset($request_data['ID']) ) {
            $errors[] = array(
                'field' => 'ID',
                'message' => __( 'Required', 'klasse-wp-poll-survey' ),
            );
        } else {
            $entry = Entry::get_as_array( $request_data['ID'] );
            if( Entry::$post_type != $entry['post_type'] ) {
                $errors[] = array(
                    'field' => 'ID',
                    'message' => __( 'Not a valid entry ID', 'klasse-wp-poll-survey' ) );
            }
        }

        static::process_get_request($request_data, $errors);
    }

    public static function process_get_request($request_data, $errors){
        if( sizeof( $errors ) > 0 ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            wp_send_json_error($errors);
        } else {
            $result_profile = static::get_result_profile( $request_data['ID'] );
            wp_send_json( $result_profile );
        }

        die();
    }

    public static function get_result_profile($entry_id){
        $version = Entry::get_version($entry_id);
        $result_profiles = Result_Profile::get_all_by_post_parent( $version['ID'] );

        $current_user_entries =
            Entry::get_all_by_session_hash_and_version( $version['ID'] );

        Session::unset_version_info( $version['ID'] );

        $sum_of_values = 0;
        foreach($current_user_entries as $entry){
            $answer_option = Answer_Option::get_as_array( $entry['post_parent'] );
            $sum_of_values += $answer_option['_kwps_answer_option_value'];
        }

        foreach($result_profiles as $result_profile){
            if ($sum_of_values >= $result_profile['_kwps_min_value']
                && $sum_of_values <= $result_profile['_kwps_max_value'] ){
                    return $result_profile;
            }
        }

        return array(
            array(
                'field' => 'All',
                'message' => __( 'No valid result profile found', 'klasse-wp-poll-survey' ),
            ),
        );
    }

    public static function get_result_profile_by_version_and_hash($version_id, $user_hash){
        $result_profiles = Result_Profile::get_all_by_post_parent( $version_id );

        $current_user_entries =
            Entry::get_all_by_user_hash_and_version($user_hash, $version_id);

        $sum_of_values = 0;
        foreach($current_user_entries as $entry){
            $answer_option = Answer_Option::get_as_array( $entry['post_parent'] );
            $sum_of_values += $answer_option['_kwps_answer_option_value'];
        }

        foreach($result_profiles as $result_profile){
            if ($sum_of_values >= $result_profile['_kwps_min_value']
                && $sum_of_values <= $result_profile['_kwps_max_value'] ){
                return $result_profile;
            }
        }

        return array(
            array(
                'field' => 'All',
                'message' => __( 'No valid result profile found', 'klasse-wp-poll-survey' ),
            ),
        );
    }


    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
        return '';
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
        return true;
    }


}