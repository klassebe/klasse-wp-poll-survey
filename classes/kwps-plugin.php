<?php

namespace kwps_classes;

require_once __DIR__ . '/post-types/test-modus.php';

class Kwps_Plugin {

    public static function on_activate( $networkwide ){
        global $wpdb;

        if( function_exists( 'is_multisite' ) && is_multisite() ) {
            if( $networkwide ) {
                $old_blog = $wpdb->blogid;
                // get all blog ids
                $blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
                foreach ($blogids as $blog_id) {
                    switch_to_blog($blog_id);
                    static::activate_single_blog();
                }
                switch_to_blog($old_blog);
                return;
            }
        }
        static::activate_single_blog();
    }

    private static function activate_single_blog() {
        Test_Modus::create_default_test_modi();
    }

    public static function on_deactivate(){

    }
} 