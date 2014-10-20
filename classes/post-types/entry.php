<?php
namespace kwps_classes;

require_once 'kwps-post-type.php';
require_once 'entry.php';

class Entry extends Kwps_Post_Type{

    public static $post_type = 'kwps_entry';

    public static $numeric_fields = array();

    public static $required_fields = array(
        'post_parent',
    );

    public static $additional_validation_methods = array(
        'check_is_allowed_by_uniqueness',
        'check_group_exists'
    );

    public static $meta_data_fields = array(
        '_kwps_sort_order',
        '_kwps_cookie_value',
        '_kwps_ip_address',
        '_kwps_session',
        '_kwps_group',
    );

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
        'exclude_from_search' => false,
        'publicly_queryable' => false,
    );

    public static function get_test_modus($entry_id)
    {
        $entry = static::get_as_array($entry_id);
        return Answer_Option::get_test_modus($entry['post_parent']);
    }

    public static function get_version($entry_id){
        $entry = static::get_as_array($entry_id);
        return Answer_Option::get_version($entry['post_parent']);
    }

    public static function get_all_of_result_group($answer_option_id, $hash){
        $all_entries = static::get_all_by_post_parent($answer_option_id);

        $filtered_entries = array();

        foreach( $all_entries as $entry ) {
            if( $entry['_kwps_group'] == $hash) {
                $filtered_entries[] = $entry;
            }
        }

        return $filtered_entries;
    }

    public static function get_html($entry_id){
        $entry = static::get_as_array($entry_id);

        return '<div class="kwps-entry">' . $entry['post_content'] . '</div>';
    }

    public static function validate_for_update($post_as_array)
    {
        return array( array( 'field' => 'All', 'message' => 'This type can never be updated' ) );
    }

    public static function validate_for_delete($entry_id = 0){
        $answer_option_id = wp_get_post_parent_id($entry_id);
        return Answer_Option::validate_for_delete($answer_option_id);
    }

    public static function save_from_request(){
        $request_data = static::get_post_data_from_request();
        foreach($request_data as $key => $value){
            $request_data[$key]['post_type'] = static::$post_type;
            $request_data[$key]['_kwps_cookie_value'] = $_COOKIE['klasse_wp_poll_survey'];
            $request_data[$key]['_kwps_ip_address'] = Uniqueness::get_ip_of_user();
            $request_data[$key]['post_author'] = get_current_user_id();

            $version = Answer_Option::get_version( $request_data[$key]['post_parent'] );
            $version_id = $version['ID'];
            $request_data[$key]['_kwps_session'] = Session::get_version_info($version_id);
        }

        $errors = static::validate_for__bulk_insert($request_data);
        static::process_request_data($request_data, $errors);
    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);

        return $request_data;
    }

    public static function process_request_data($request_data, $errors){
        if( sizeof( $errors ) > 0 ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            wp_send_json_error($errors);
        } else {
            $posts = array();
            foreach($request_data as $data){
                $post = static::save_post($data);
                $posts[] = $post;
            }
            wp_send_json( $posts );
        }

        die();
    }

    static function validate_for__bulk_insert($array_of_entries = array()) {
        foreach($array_of_entries as $entry){
            $errors = static::validate_for_insert( $entry );

            foreach($errors as $key => $value){
                if( isset($entry['post_parent']) ){
                    $errors[$key]['post_parent'] = $entry['post_parent'];
                }
            }
        }

        return $errors;
    }

    public static function check_is_allowed_by_uniqueness($post){
        $errors = array();

        if( isset( $post['post_parent'] ) ){
            $answer_option = Answer_Option::get_as_array($post['post_parent']);
            $question = Question::get_as_array($answer_option['post_parent']);
            $question_group = Question_Group::get_as_array($question['post_parent']);
            $version = Version::get_as_array($question_group['post_parent']);

            $limitations = Test_Collection::get_meta_data($version['post_parent']);

            if( is_user_logged_in() ){
                if( ! Uniqueness::is_allowed($question['ID'], $limitations['_kwps_logged_in_user_limit']) ){
                    array_push( $errors, array('field' => 'All', 'message' => 'You have reached the limit to participate') );
                }
            } else {
                if( ! Uniqueness::is_allowed($question['ID'], $limitations['_kwps_logged_out_user_limit']) ){
                    array_push( $errors, array('field' => 'All', 'message' => 'You have reached the limit to participate') );
                }
            }
        }

        return $errors;
    }

    public static function check_group_exists($post) {
        $errors = array();

        if( isset( $post['_kwps_group'] ) && isset( $post['post_parent'] ) ) {
            $version = Answer_Option::get_version( $post['post_parent'] );
            $test_collection = Test_Collection::get_as_array( $version['post_parent'] );
            $result_groups = Result_Group::get_all_by_post_parent( $test_collection['ID'] );

            $group_exists = false;

            foreach( $result_groups as $result_group ) {
                if( $post['_kwps_group'] == $result_group['_kwps_hash'] ) {
                    $group_exists = true;
                    break;
                }
            }

            if( !$group_exists ) {
                array_push( $errors, array('field' => '_kwps_group', 'message' => 'Not a valid group given') );
            }
        }

        return $errors;
    }

    static function is_part_of_completed_test($entry_id){
        $version = static::get_version( $entry_id );
        $user_hashes = static::get_all_user_hashes_per_version( $version['ID'] );

        foreach($user_hashes as $user_hash){
            $user_entries = static::get_all_by_user_hash_and_version($user_hash, $version['ID']);

            if( sizeof( $user_entries ) < Question::get_count_per_version($version['ID']) ) {
                return false;
            }
        }

        return true;
    }

    static function get_all_user_hashes_per_version($version_id, $group=''){
        $user_hashes = array();

        $question_groups = Question_Group::get_all_by_post_parent($version_id);
        foreach($question_groups as $question_group){
            $questions = Question::get_all_by_post_parent($question_group['ID']);
            foreach($questions as $question){
                $answer_options = Answer_Option::get_all_by_post_parent($question['ID']);
                foreach($answer_options as $answer_option){
                    $entries = Entry::get_all_by_post_parent($answer_option['ID']);
                    foreach($entries as $entry){
                        if( sizeof( $group ) > 0 ) {
                            if( isset( $entry['_kwps_group'] ) && $entry['_kwps_group'] == $group) {
                                array_push( $user_hashes,  $entry['_kwps_cookie_value']);
                            }
                        } else {
                            array_push( $user_hashes,  $entry['_kwps_cookie_value']);
                        }
                    }
                }
            }
        }

        return array_unique($user_hashes);
    }

    static function get_all_by_user_hash_and_version($user_hash, $version_id){
        $args = array(
            'post_type' => static::$post_type,
            'meta_query' => array(
                array(
                    'key' => '_kwps_cookie_value',
                    'value' => $user_hash,
                ),
            ),
            'post_status' => array( 'draft', 'publish'),
        );
        $entries_by_user_hash = get_posts( $args );
        $entries_by_user_hash_with_meta = array();

        foreach($entries_by_user_hash as $entry_by_user_hash){
            array_push( $entries_by_user_hash_with_meta, static::get_as_array( $entry_by_user_hash->ID ) );
        }


        $entries = array();

        foreach($entries_by_user_hash_with_meta as $entry){
            $version = static::get_version( $entry['ID'] );
            if( $version['ID'] == $version_id ) {
                array_push($entries, $entry);
            }
        }

        return $entries;
    }

    static function get_all_by_session_hash_and_version($version_id){
        $args = array(
            'post_type' => static::$post_type,
            'meta_query' => array(
                array(
                    'key' => '_kwps_session',
                    'value' => Session::get_version_info($version_id),
                ),
            ),
            'post_status' => array( 'draft', 'publish'),
        );
        $entries_by_user_hash = get_posts( $args );
        $entries_by_user_hash_with_meta = array();

        foreach($entries_by_user_hash as $entry_by_user_hash){
            array_push( $entries_by_user_hash_with_meta, static::get_as_array( $entry_by_user_hash->ID ) );
        }


        $entries = array();

        foreach($entries_by_user_hash_with_meta as $entry){
            $version = static::get_version( $entry['ID'] );
            if( $version['ID'] == $version_id ) {
                array_push($entries, $entry);
            }
        }

        return $entries;
    }

	public static function delete_from_version() {

		if(!current_user_can('edit_posts')) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', true, 403);
			wp_send_json_error(
                array(
                    'field' => 'All',
                    'error' => __( 'You have no permissions', 'klasse-wp-poll-survey' ) ,
                )
            );
		}

		$request_data = static::get_post_data_from_request();
		$version_id = $request_data['post_parent'];

		$user_hashes = self::get_all_user_hashes_per_version( $version_id );

		$i = 0;
		foreach( $user_hashes as $user_hash ){
			$user_entries = static::get_all_by_user_hash_and_version( $user_hash, $version_id );

			foreach( $user_entries as $user_entry ) {
				$user_entry['post_status'] = 'trash';
				static::save_post( $user_entry );
				$i++;
			}
		}

		$version = static::get_as_array( $version_id );
		$version['_kwps_view_count'] = 0;
		static::save_post( $version );

		wp_send_json( array('success' => true, 'count' => $i) );

	}

}

/* EOF */