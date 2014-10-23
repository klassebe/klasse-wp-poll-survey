<?php

namespace kwps_classes;

require_once __DIR__ . '/post-type-interface.php';

/**
 * Class Kwps_Post_Type
 * @package kwps_classes
 */
abstract class Kwps_Post_Type implements \kwps_classes\Post_Type_Interface {

    /**
     * @var array contains all meta data keys for which a value is required
     */
    public static $required_fields = array();

    /**
     * @var array contains all meta data keys for which the value should be a number
     */
    public static $numeric_fields = array('_kwps_sort_order');

    /**
     * @var array contains names of all the addional validation methods that need to run
     */
    public static $additional_validation_methods = array();

    /**
     * @var array contains all meta data keys
     */
    public static $meta_data_fields = array('_kwps_sort_order');

    /**
     * @var string slug of the post type, used to register the post type
     */
    public static $post_type = '';

    /**
     * @var array contains the settings for the rewrite rules when registering the post type
     */
    public static $rewrite = array(
            'slug' => '',
            'with_front' => false,
        );

    /**
     * @var array contains all arguments to register the post type
     */
    public static $post_type_args = array();

    /**
     * Registers the post type
     */
    public static function register_post_type(){
        $post_type_args = static::$post_type_args;
        $post_type_args['rewrite'] = static::$rewrite;

        register_post_type(static::$post_type, $post_type_args);
    }

    /**
     * Returns post as an associative array, meta data is automatically included if found
     * Returns false when no post found
     *
     * @param $post_id
     * @return array|bool|null|\WP_Post
     */
    public static function get_as_array($post_id){
        $post_as_array = get_post($post_id,  ARRAY_A);

         if(null == $post_as_array){
             $post_as_array = false;
         } else {
             $post_as_array = array_merge($post_as_array, static::get_meta_data($post_id));
         }
        return $post_as_array;
    }

    /**
     * Returns all meta data for the post as an array, empty array if no meta data found
     *
     * @param $post_id
     * @return array
     */
    public static function get_meta_data($post_id)
    {
        $meta_as_array = array();

        foreach(static::$meta_data_fields as $field){
            $meta_as_array[$field] = get_post_meta($post_id, $field, true);
	        if(in_array($field, static::$numeric_fields)) {
		        $meta_as_array[$field] = (int) $meta_as_array[$field];
	        }
        }

        return $meta_as_array;
    }

    /**
     * Returns an array containing all posts of post_type with given post_parent
     *
     * @param $post_parent_id
     * @return array
     */
    public static function get_all_by_post_parent($post_parent_id){
        $args = array('post_type' => static::$post_type,
            'posts_per_page' => -1,
            'post_parent' => $post_parent_id,
            'post_status'	=> array('draft', 'publish'),
            'nopaging' => true,
        );

        if( in_array('_kwps_sort_order', static::$required_fields) ){
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            $args['meta_key'] = '_kwps_sort_order';

        }

        $child_objects = get_posts($args);

        $children = array();

        foreach($child_objects as $object){
            array_push($children, static::get_as_array($object->ID));
        }
        return $children;
    }

    /**
     * Returns the first post found of post_type with give post_parent.
     * Returns null if none found.
     *
     * @param $post_parent_id
     * @return null
     */
    public static function get_one_by_post_parent($post_parent_id){
        $children = static::get_all_by_post_parent($post_parent_id);

	    if(!empty($children)) {
		    $child = $children[0];
	    } else {
		    $child = null;
	    }
        return $child;
    }

    /**
     * Validates the post given via a POST request in json and if validated saves it to the database.
     * Sends back a json response indicating success or failure and error messages in case of errors.
     *
     */
    public static function save_from_request(){

        $request_data = static::get_post_data_from_request();
        $errors = static::validate_for_insert($request_data);

        static::process_request_data($request_data, $errors);
    }

    /**
     * Returns the decoded json data from a http request
     *
     * @return array|mixed
     */
    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);
        $request_data['post_type'] = static::$post_type;

        return $request_data;
    }

    /**
     * Validates the post according to the rules for insertion, returns errors when post is not valid.
     *
     * @param array $post_as_array
     * @return array
     */
    static function validate_for_insert($post_as_array = array()) {
        $errors = static::check_required_fields($post_as_array);
        $errors = array_merge($errors, static::check_numeric_fields($post_as_array));

        foreach(static::$additional_validation_methods as $validation_method){
            $errors = array_merge( $errors, static::$validation_method( $post_as_array ) );
        }

        if( isset( $post_as_array['post_status'] ) && 'publish' == $post_as_array['post_status']  ){
            $errors = array_merge( $errors, static::validate_for_publish( $post_as_array ) );
        }

        return $errors;
    }

    /**
     * Returns an empty array when all required fields are present and case of a string their length is not 0.
     * Returns an array of errors otherwise
     *
     * @param $post
     * @return array
     */
    public static function check_required_fields($post){
        $errors = array();
        foreach(static::$required_fields as $field){
            if(! isset($post[$field])) {
                array_push($errors,
                    array(
                        'field' => $field,
                        'message' => __( 'Required', 'klasse-wp-poll-survey' )
                    )
                );
            } else {
                if( is_string($post[$field])){
                    if( strlen($post[$field]) == 0 ) {
                        array_push($errors,
                            array(
                                'field' => $field,
                                'message' => __( 'Required', 'klasse-wp-poll-survey' )
                            )
                        );
                    }
                }
            }
        }
        return $errors;
    }

    /**
     * Returns an empty array when all numeric fields have a numeric value.
     * Returns an array of errors otherwise.
     *
     * @param $post
     * @return array
     */
    public static function check_numeric_fields($post){
        $errors = array();
        foreach(static::$numeric_fields as $field){
            if( isset( $post[$field]) ) {
                if(! is_numeric( $post[$field] ) ){
                    array_push( $errors ,
                        array(
                            'field' => $field,
                            'message' => __( 'Needs to be a number', 'klasse-wp-poll-survey' )
                        )
                    );
                }
            }
        }

        return $errors;
    }

    /**
     *  Saves the post, when a field is prefixed with '_kwps' it will be saved as meta data
     *  Returns the saved post or null on failure.
     *
     * @param $post_data
     * @return array|null
     */
    public static function save_post($post_data, $return_id = false){
        $post_data['post_type'] = static::$post_type;
        $post_id = wp_insert_post($post_data);

        if( $post_id != 0 ){
            foreach($post_data as $field => $value){
                if( strpos($field, 'kwps') ) {
                    update_post_meta($post_id, $field, $value);
                }
            }
        } else {
            return null;
        }

        if( $return_id ) {
            return $post_id;
        } else {
            return static::get_as_array($post_id);
        }
    }

    /**
     * Sends a json response to a request, will try to save the request_data as a post depending on the value of errors.
     * In case of errors http status code 400 Bad Request is sent back.
     *
     * @param $request_data
     * @param $errors
     */
    public static function process_request_data($request_data, $errors){
        if( sizeof( $errors ) > 0 ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            wp_send_json_error($errors);
        } else {
            $post = static::save_post($request_data);
            wp_send_json( $post );
        }

        die();
    }

    /**
     * Validates the post given via a POST request in json and if validated updates it in the database.
     * Sends back a json response indicating success or failure and error messages in case of errors.
     *
     */
    public final static function update_from_request(){
        $request_data = static::get_post_data_from_request();
        $errors = static::validate_for_update($request_data);

        static::process_request_data($request_data, $errors);
    }

    /**
     * Validates the post according to the rules for updating, returns errors when post is not valid.
     *
     * @param $request_data
     * @return array
     */
    public static function validate_for_update($request_data){
        $errors = array();
        if( isset( $request_data['ID'] ) ) {
            $orig_post = static::get_as_array($request_data['ID']);
            if( $orig_post['post_status'] == 'publish' && $request_data['post_status'] != 'locked' ) {
                $errors[] = array(
                    'field' => 'All',
                    'Message' => __( 'You cannot update once published', 'klasse-wp-poll-survey' ) );
            } elseif( $orig_post['post_status'] == 'locked' && $request_data['post_status'] != 'publish'){
                $errors[] = array(
                    'field' => 'All',
                    'Message' => __( 'You cannot update once locked', 'klasse-wp-poll-survey' ) );
            } else {
                $errors = static::validate_for_insert($request_data);
            }
        } else {
            $errors[] = array(
                'field' => 'ID',
                'message' => __( 'Required', 'klasse-wp-poll-survey' ) );
        }
        return $errors;
    }

    /**
     * Returns an associative array, with self-explanatory keys:<br>
     * allow_publish (boolean)<br>
     * errors (array)<br>
     *
     * @param $post
     * @return array
     */
    public static function validate_for_publish($post){
        return array();
    }

    /**
     * Deletes a post of post_type, and all it's metadata from database
     *
     */
    public final static function delete_from_request(){
        $request_data = static::get_post_data_from_request();

        if(static::validate_for_delete()){
            wp_delete_post($request_data['ID']);
        }
        static::delete_meta($request_data['ID']);
    }

    /**
     * Deletes all meta data of post with post_id
     *
     * @param int $post_id
     */
    public static final function delete_meta($post_id = 0){

        foreach(get_post_meta($post_id) as $meta_key => $meta_value){
            delete_post_meta($post_id, $meta_key);
        }
    }


}