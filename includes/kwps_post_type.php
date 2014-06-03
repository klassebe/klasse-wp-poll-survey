<?php

namespace includes;

require_once __DIR__ . '/post_type_interface.php';

abstract class Kwps_Post_Type implements \includes\Post_Type_Interface {

    public static $required_fields = array();

    public static $numeric_fields = array();

    public static $meta_data_fields = array('_kwps_sort_order');

    public static $post_type = '';

    public static $rewrite = array(
            'slug' => '',
            'with_front' => false,
        );

    public static $post_type_args = array();

    public static function register_post_type(){
        $post_type_args = static::$post_type_args;
        $post_type_args['rewrite'] = static::$rewrite;

        register_post_type(static::$post_type, $post_type_args);
    }

    public static function get_as_array($post_id){
        $post_as_array = get_post($post_id,  ARRAY_A);

         if(null == $post_as_array){
             $post_as_array = false;
         } else {
             $post_as_array = array_merge($post_as_array, static::get_meta_data($post_id));
         }
        return $post_as_array;
    }

    public static function get_meta_data($post_id)
    {
        $meta_as_array = array();

        foreach(static::$meta_data_fields as $field){
            $meta_as_array[$field] = get_post_meta($post_id, $field, true);
        }

        return $meta_as_array;
    }

    public static function get_all_by_post_parent($test_id){
        $child_objects = get_posts( array('post_type' => static::$post_type,
            'post_parent' => $test_id,
            'orderby' => 'meta_value_num',
            'meta_key' => '_kwps_sort_order',
            'post_status'	=> array('draft', 'publish')
        ) );

        $children = array();

        foreach($child_objects as $object){
            array_push($children, static::get_as_array($object->ID));
        }
        return $children;
    }

    public static function get_one_by_post_parent($test_id){
        $children = static::get_all_by_post_parent($test_id);

	    if(!empty($children)) {
		    $child = $children[0];
	    } else {
		    $child = null;
	    }
        return $child;
    }

    public static function save_from_request(){

        $request_data = static::get_post_data_from_request();
        $errors = static::validate_for_insert($request_data);

        static::process_request_data($request_data, $errors);
    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);
        $request_data['post_type'] = static::$post_type;

        return $request_data;
    }

    static function validate_for_insert($post_as_array = array()) {
        $errors = static::check_required_fields($post_as_array);
        $errors = array_merge($errors, static::check_numeric_fields($post_as_array));

        return $errors;
    }

    public static function check_required_fields($post){
        $errors = array();
        foreach(static::$required_fields as $field){
            if(! isset($post[$field])) {
                array_push($errors, array( 'field' => $field, 'message' => 'Required') );
            } else {
                if( is_string($post[$field])){
                    if( strlen($post[$field]) == 0 ) {
                        array_push($errors, array( 'field' => $field, 'message' => 'Required') );
                    }
                }
            }
        }
        return $errors;
    }

    public static function check_numeric_fields($post){
        $errors = array();
        foreach(static::$numeric_fields as $field){
            if( isset( $post[$field]) ) {
                if(! is_numeric( $post[$field] ) ){
                    array_push( $errors , array( 'field' => $field, 'message' => 'Needs to be a number') );
                }
            }
        }

        return $errors;
    }

    public static function save_post($post_data){
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

        return static::get_as_array($post_id);
    }

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

    public final static function update_from_request(){
        $request_data = static::get_post_data_from_request();
        if(static::validate_for_update($request_data)){
            wp_send_json( static::save_post($request_data) );
        } else {
            wp_send_json(null);
        }

        die();
    }

    public final static function delete_from_request(){
        $request_data = static::get_post_data_from_request();

        if(static::validate_for_delete()){
            wp_delete_post($request_data['ID']);
        }
        static::delete_meta($request_data['ID']);
    }

    public static final function delete_meta($post_id = 0){

        foreach(get_post_meta($post_id) as $meta_key => $meta_value){
            delete_post_meta($post_id, $meta_key);
        }
    }


}