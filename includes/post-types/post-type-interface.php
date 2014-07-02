<?php
    namespace includes;

    interface Post_Type_Interface{
        public static function register_post_type();

        public static function validate_for_insert($post_as_array = array());
        public static function validate_for_update($post_as_array);
        public static function validate_for_delete($post_id = 0);

        public static function delete_meta($post_id);
        public static  function get_html($id);
        public static function get_meta_data($post_id);

        public static function get_test_modus($post_id);
    }