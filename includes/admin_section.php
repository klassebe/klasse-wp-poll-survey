<?php
namespace includes;


class admin_section {
    public static function display_tests(){
        $arguments = array(
            'post_type' => 'kwps_poll',
//            'post_parent' => 1,
        );
        $tests = get_posts($arguments);
        foreach($tests as $test){
            $meta_data = get_post_meta($test->ID);
//            var_dump($meta_data); die;
            foreach($meta_data as $key => $value){
                $test->$key = $value;
            }
        }
        var_dump($tests); die;
    }
} 