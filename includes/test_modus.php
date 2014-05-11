<?php
    namespace includes;

    abstract class Test_Modus {
        public static $input_types = array(
            'custom_options',   // radio buttons
            'scale',            // radio buttons with predetermined options (letters or numbers)
            'multiple_choice',  // radio buttons, each answer option is tied to a letter
            'scored_options',   // radio buttons, each answer option has a numerical value
            'quiz',             // radio buttons, there is only one correct answer among the options
            'links'

        );
        public static $output_types = array();

        public static $post_type = 'kwps_test_modus';

        public static $rewrite = array(
            'slug' => 'testmodi',
            'with_front' => false,
        );

        public static $post_type_args = array(
            'public' => true,
            'supports' => array(
                'title',
            ),
            'labels' => array(
                'name' => 'Test Modi',
                'singular_name' => 'Test Modus',
                'add_new' => 'Add New Test Modus',
                'add_new_item' => 'Add New Test Modus',
                'edit_item' => 'Edit Test Modus',
                'new_item' => 'New Test Modus',
                'view_item' => 'View Test Modus',
                'search_items' => 'Search Test Modi',
                'not_found' => 'No Test Modi Found',
                'not_found_in_trash' => 'No Test Modi Found In Trash',
            ),
            'show_in_menu' => true,
            'show_ui' => true,
            'hierarchical' => false,
            'publicly_queryable' => false,
        );

        public static function register_post_type(){
            $post_type_args = static::$post_type_args;
            $post_type_args['rewrite'] = static::$rewrite;

            register_post_type(static::$post_type, $post_type_args);
        }

        public static function get_rules($post_type = ''){
            $args = array('post_title' => $post_type);
            $posts = get_posts($args);

            if(sizeof($posts) < 1){
                return false;
            } else {
                $max_questions = get_post_meta($posts[0]['ID'], '_kwps_max_questions', true);
                $max_answer_options_per_question = get_post_meta($posts[0]['ID'], '_kwps_max_answer_options_per_question', true);
                $allowed_input_types = get_post_meta($posts[0]['ID'], '_kwps_allowed_input_types', true);
                $allowed_output_types = get_post_meta($posts[0]['ID'], '_kwps_allowed_output_types', true);
                return array(
                    'max_questions' => $max_questions,
                    'max_answer_options_per_question' => $max_answer_options_per_question,
                    'allowed_input_types' => $allowed_input_types,
                    'allowed_output_types' => $allowed_output_types,
                );
            }
        }

        public static function validate_for_insert(){
            global $post;

            if(strlen($post->post_title) == 0){
                return false;
            } else {
                if(static::has_duplicate($post->ID, $post->post_title)) {
                    return false;
                }
            }


        }

        public static function has_duplicate($id, $title){
            $args = array(
                'post_type' => static::$post_type,
                'post_status' => 'publish',
            );

            $posts = get_posts($args);
            if( sizeof($posts) > 0 ){
//                var_dump($posts);
                foreach($posts as $post){
                    if($post->ID != $id && $post->post_title == $title){
                        return true;
                    }
                }
                return false;
            } else {
                return false;
            }
        }
    }