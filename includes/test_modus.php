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

        public static function set_to_duplicate_when_title_exists($status){
            if( isset($_POST) && sizeof($_POST) > 0 ) {
                global $post;

                if($post->post_type == 'kwps_test_modus'){
                    if(! static::title_length_is_ok() ){
                        $status = 'draft';
                    } elseif ( static::has_duplicate() ){
                        $status = 'duplicate';
                    } elseif( isset($_POST['publish']) && current_user_can( 'publish_posts' )){
                        $status = 'publish';
                    }
                }
            }
            return $status;


        }

        private static function title_length_is_ok(){
            global $post;

            return strlen($post->post_title) > 0;
        }

        public static function validate_for_insert(){
            if( ! static::title_length_is_ok() ){
                return false;
            } else {
                if( static::has_duplicate() ) {
                    return false;
                }
            }


        }

        public static function has_duplicate(){
            global $post;

            $args = array(
                'post_type' => static::$post_type,
                'post_status' => 'publish',
            );

            $posts = get_posts($args);

            if( sizeof($posts) > 0 ){
                foreach($posts as $retrieved_post){
                    if($retrieved_post->ID != $post->ID && $retrieved_post->post_title == $post->post_title){
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

            if ( $current_screen->parent_base == 'edit' && $post->post_type == 'kwps_test_modus'){
                if(strlen($post->post_title) == 0){
                    echo '<div class="error"><p>Post saved as draft - Title is empty</p></div>';
                }

                if( \includes\Test_Modus::has_duplicate($post->ID, $post->post_title)){
                    echo '<div class="error">';
                    echo '<p>Test Modus was saved as duplicate - new settings will not be used</p>';
                    echo '<p>Either rename this Test Modus or remove the Test Modus already in use</p>';
                    echo '</div>';
                }
            }
        }

        public static function get_published_modi(){
            $args = array(
                'post_type' => static::$post_type,
                'post_status' => 'publish',
                'numberposts' => -1,
            );

            $posts = (array) get_posts($args);
            return $posts;
        }
    }