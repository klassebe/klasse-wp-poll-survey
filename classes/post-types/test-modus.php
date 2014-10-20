<?php
    namespace kwps_classes;

    abstract class Test_Modus extends Kwps_Post_Type{

        public static $required_fields = array(
            'post_title',
            '_kwps_max_question_groups',
            '_kwps_max_questions_per_question_group',
            '_kwps_max_answer_options_per_question',
            '_kwps_allowed_input_types',
            '_kwps_allowed_output_types',
            '_kwps_answer_options_require_value',
        );

        public static $numeric_fields = array(
            '_kwps_max_question_groups',
            '_kwps_max_questions_per_question_group',
            '_kwps_max_answer_options_per_question',
        );

        public static $answer_option_types = array(
            'free-text-only',   // free text, no value assigned
            'scale',            // free text, each question has the same answer_options, free text + integer value
            'multiple_choice',  // same as free text but on display each answer option is listed with a letter next to it
            //'scored_options',   // free text, each answer option has a numerical value
        );

        public static $meta_data_fields = array(
            '_kwps_max_question_groups',
            '_kwps_max_questions_per_question_group',
            '_kwps_max_answer_options_per_question',
            '_kwps_allowed_input_types',
            '_kwps_allowed_output_types',
            '_kwps_allowed_output_types_test_collection',
            '_kwps_answer_options_require_value',
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
            'show_in_menu' => false,
            'show_ui' => false,
            'hierarchical' => false,
            'publicly_queryable' => false,
        );

        public static function register_post_type(){
            $post_type_args = static::$post_type_args;
            $post_type_args['rewrite'] = static::$rewrite;

            register_post_type(static::$post_type, $post_type_args);
        }

        public static function set_to_duplicate_when_title_exists($status){
            if( isset($_POST) && sizeof($_POST) > 0 ) {
                global $post;
//                var_dump($post);

                if(isset($post) &&'kwps_test_modus' == $post->post_type){
                    if(! static::title_length_is_ok() ){
                        $status = 'draft';
                    } elseif ( static::has_duplicate() ){
                        $status = 'duplicate';
                    } elseif( isset($_POST['publish']) && current_user_can( 'publish_posts' )){
                        $status = 'publish';
                    }
                }
            }
//            var_dump($status); die;
            return $status;


        }

        public static function create_default_test_modi(){
            $kwps_poll = array(
                'post_title' => 'Poll',
                'post_content' => 'Description for Poll',
                'post_name' => 'kwps-poll',
                'post_status' => 'publish',
                'post_type' => 'kwps_test_modus',
                '_kwps_max_question_groups' => 1,
                '_kwps_max_questions_per_question_group' => 1,
                '_kwps_max_answer_options_per_question' => -1,
                '_kwps_allowed_input_types' => array('input_type_1', 'input_type_2'),
                '_kwps_allowed_output_types' => array( 'bar-chart-per-question', 'pie-chart-per-question' ),
                '_kwps_allowed_output_types_test_collection' => array( 'bar-chart-per-question', 'pie-chart-per-question'),
                '_kwps_answer_options_require_value' => 0,
            );

            $kwps_personality_test = array(
                'post_title' => 'Personality test',
                'post_content' => 'Description for Personality test',
                'post_name' => 'kwps-personality-test',
                'post_status' => 'publish',
                'post_type' => 'kwps_test_modus',
                '_kwps_max_question_groups' => -1,
                '_kwps_max_questions_per_question_group' => -1,
                '_kwps_max_answer_options_per_question' => -1,
                '_kwps_allowed_input_types' => array('input_type_1', 'input_type_2'),
                '_kwps_allowed_output_types' => array( 'result-profile'),
                '_kwps_allowed_output_types_test_collection' => array( 'grouped-bar-chart-per-profile'),
                '_kwps_answer_options_require_value' => 1,
            );

            $kwps_survey = array(
                'post_title' => 'Survey',
                'post_content' => 'Description for Survey',
                'post_name' => 'kwps-survey',
                'post_status' => 'publish',
                'post_type' => 'kwps_test_modus',
                '_kwps_max_question_groups' => -1,
                '_kwps_max_questions_per_question_group' => -1,
                '_kwps_max_answer_options_per_question' => -1,
                '_kwps_allowed_input_types' => array('input_type_1', 'input_type_2'),
                '_kwps_allowed_output_types' => array( 'bar-chart-per-question', 'pie-chart-per-question' ),
                '_kwps_allowed_output_types_test_collection' => array( 'bar-chart-per-question', 'pie-chart-per-question'),
                '_kwps_answer_options_require_value' => -1,
            );

            if( ! static::default_test_modus_exists($kwps_poll) ){
                $error = static::save_post($kwps_poll);
            }

            if( isset($error) && null == $error ){
                //TODO add html to report error
                var_dump($error);
            }

            if( ! static::default_test_modus_exists($kwps_personality_test) ){
                $error = static::save_post($kwps_personality_test);
            }

            if( isset($error) && null == $error ){
                //TODO add html to report error
                var_dump($error);
            }

            if( ! static::default_test_modus_exists($kwps_survey) ){
                $error = static::save_post($kwps_survey);
            }

            if( isset($error) && null == $error ){
                //TODO add html to report error
                var_dump($error);
            }
        }

        private static function title_length_is_ok(){
            global $post;

            return strlen($post->post_name) > 0;
        }

        public static function has_duplicate(){
            global $post;

//            var_dump($_POST);

            $args = array(
                'post_type' => static::$post_type,
                'post_status' => 'publish',
            );

            $posts = get_posts($args);

            if( sizeof($posts) > 0 ){
                foreach($posts as $retrieved_post){
                    if($retrieved_post->ID != $post->ID && $retrieved_post->post_name == $post->post_name){
                        return true;
                    }
                }
                return false;
            } else {
                return false;
            }
        }

        public static function default_test_modus_exists($post){

            $args = array(
                'post_type' => static::$post_type,
                'post_status' => 'publish',
            );

            $posts = get_posts($args);

            if( sizeof($posts) > 0 ){
                foreach($posts as $retrieved_post){
//                    if($retrieved_post->ID != $post->ID && $retrieved_post->post_title == $post->post_title){
                    if( $retrieved_post->post_name == $post['post_name'] ){
                        return true;
                    }
                }
                return false;
            } else {
                return false;
            }
        }

        public static function admin_notices(){
            global $current_screen, $post;

            if( isset($post) ){
                if ( $current_screen->parent_base == 'edit' && $post->post_type == 'kwps_test_modus'){
                    if(strlen($post->post_title) == 0){
                        echo '<div class="error"><p>' . __('Post saved as draft - Title is empty', 'klasse-wp-poll-survey') . '</p></div>';
                    }

                    if( \kwps_classes\Test_Modus::has_duplicate($post->ID, $post->post_name)){
                        echo '<div class="error">';
                        echo '<p>' . __('Test Modus was saved as duplicate - new settings will not be used', 'klasse-wp-poll-survey') . '</p>';
                        echo '<p>' . __('Either rename this Test Modus or remove the Test Modus already in use', 'klasse-wp-poll-survey') . '</p>';
                        echo '</div>';
                    }
                }
            }
        }

        public static function get_published_modi(){
            $args = array(
                'post_type' => static::$post_type,
                'post_status' => 'publish',
                'numberposts' => -1,
            );

            $posts = get_posts($args);

            $posts_with_meta = array();

            foreach($posts as $post){
                $post_with_meta = static::get_as_array($post->ID);
                array_push($posts_with_meta, $post_with_meta);
            }
            return $posts_with_meta;
        }
    }