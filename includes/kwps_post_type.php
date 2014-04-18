<?php

namespace includes;

abstract class Kwps_Post_Type{

    public static $post_type = 'kwps_post_type';

    public static $post_type_args = array();



    public static function validate_for_update($post_as_array){
        return true;
    }

    public static function delete_meta(){
//           do stuff
    }
    public static function get_html($id){
        return '';
    }

    public static function register_post_type(){
        register_post_type(static::$post_type, static::$post_type_args);
    }

    public static function get_as_array($post_id){
        $post_as_array = get_post($post_id,ARRAY_A);

        if(null == $post_as_array){
            $post_as_array = false;
        } else {
            $post_as_array = array_merge($post_as_array, static::get_meta_data($post_id));
        }


        return $post_as_array;
    }

    public static function get_meta_data($post_id){
        return array('test');
    }

    public static function get_all($test_id){
        $child_objects = get_posts(array('post_type' => static::$post_type, 'post_parent' => $test_id));

        $children = array();

        foreach($child_objects as $object){
            array_push($children, static::get_as_array($object->ID));
        }

        return $children;
    }


    public static function save(){
        $json = file_get_contents("php://input");
        $post = json_decode($json, true);

        if( static::validate_for_insert($post) ) {
//            echo 'validated';
        }

        static::save_post($post);

        die();
    }

    public static function validate_for_insert($post_as_array = array()){
        return true;
    }


    public static function save_post($post_data){
        $post_id = wp_insert_post($post_data);

        $post = get_post($post_id);

        if( $post_id != 0 ){
            foreach($post_data as $field => $value){
                if( strpos($field, 'kwps') ) {
                    update_post_meta($post_id, $field, $value);
                }
            }
        } else {
            wp_send_json(null);
        }

        wp_send_json($post);
    }

    public static function update(){
        $json = file_get_contents("php://input");
        $post_data = json_decode($json, true);

        if(static::validate_for_update($post_data)){
            static::save_post($_POST);
        }
    }


    public static function delete_poll(){
        static::delete_meta();

        wp_delete_post($_POST['ID']);
    }


}