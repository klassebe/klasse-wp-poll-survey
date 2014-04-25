<?php

namespace includes;

require_once __DIR__ . '/post_type_interface.php';

abstract class Kwps_Post_Type implements \includes\Post_Type_Interface{

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
        $post_as_array = get_post($post_id,ARRAY_A);

        // if(null == $post_as_array){
        //     $post_as_array = false;
        // } else {
        //     $post_as_array = array_merge($post_as_array, static::get_meta_data($post_id));
        // }


        return $post_as_array;
    }


    public static function get_all_children($test_id){
        $child_objects = get_posts( array('post_type' => static::$post_type,
            'post_parent' => $test_id,
            'orderby' => 'meta_value_num',
            'meta_key' => '_kwps_sort_order',
        ) );

        $children = array();

        foreach($child_objects as $object){
            array_push($children, static::get_as_array($object->ID));
        }
        return $children;
    }


    public static function save_from_request(){

        $request_data = static::get_post_data_from_request();
        if( static::validate_for_insert($request_data) ) {
            wp_send_json( static::save_post($request_data) );
        } else {
            wp_send_json(null);
        }

        die();
    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);
        $request_data['post_type'] = static::$post_type;

        return $request_data;
    }



    public final static function save_post($post_data){
        $post_id = wp_insert_post($post_data);

        $post = get_post($post_id);

        if( $post_id != 0 ){
            foreach($post_data as $field => $value){
                if( strpos($field, 'kwps') ) {
                    update_post_meta($post_id, $field, $value);
                }
            }
        } else {
            return null;
        }

        return $post;
    }

    public final static function update_from_request(){
        $request_data = static::get_post_data_from_request();

        if(static::validate_for_update($request_data)){
            static::save_post($request_data);
        }
    }


    public final static function delete_from_request(){
        $request_data = static::get_post_data_from_request();

        if(static::validate_for_delete()){
            wp_delete_post($request_data['ID']);
        }
        static::delete_meta();
    }

    public static final function delete_meta($post_id = 0){

        foreach(get_post_meta($post_id) as $meta_key => $meta_value){
            delete_post_meta($post_id, $meta_key);
        }
    }


}