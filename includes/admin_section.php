<?php

namespace includes;
require_once __DIR__ . '/poll_list_table.php';


class admin_section {
    public static function display_tests_temp(){
        $arguments = array(
            'post_type' => 'kwps_poll',
//            'post_parent' => 1,
        );

        $tests_as_arrays = array();

        $tests = get_posts($arguments);
        foreach($tests as $test){
            $meta_keys = get_post_custom_keys($test->ID);
//            var_dump($meta_data); die;
            foreach($meta_keys as $key){
                $meta_data = get_post_meta($test->ID, $key, true);
                $test->$key = $meta_data;
            }

            array_push($tests_as_arrays, (array) $test);
        }

        $json_output = json_encode($tests_as_arrays);
//        wp_send_json($json_output);

        var_dump($tests_as_arrays); die;
    }

    public static function display_tests() {
        $poll_list = new Poll_List_Table();
        $poll_list->prepare_items();

        include_once __DIR__ . '/../views/poll_list.php';
    }
} 