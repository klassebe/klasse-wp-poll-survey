<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 26/03/14
 * Time: 14:04
 */

namespace includes;


class Poll {
    /**
     *
     */
    static function register_post_type(){
        $poll_args = array(
            'public' => true,
            'rewrite' => array(
                'slug' => 'polls',
                'with_front' => false,
            ),
            'supports' => array(
                'title',
            ),
            'labels' => array(
                'name' => 'Polls',
                'singular_name' => 'Poll',
                'add_new' => 'Add New Poll',
                'add_new_item' => 'Add New Poll',
                'edit_item' => 'Edit Poll',
                'new_item' => 'New Poll',
                'view_item' => 'View Poll',
                'search_items' => 'Search Polls',
                'not_found' => 'No Polls Found',
                'not_found_in_trash' => 'No Polls Found In Trash',
            ),
            'show_in_menu' => true,
//        'show_in_menu' => 'klasse-wp-poll-survey_tests',
//            'show_ui' => false,
            'hierarchical' => true,
        );

        register_post_type('kwps_poll', $poll_args);

    }

    /**
     *
     */
    static function add_metaboxes() {
        add_meta_box('kwps_intro_and_outro', 'Intro en Outro', array('\includes\poll', 'display_intro_and_outro_metabox'), 'kwps_poll', 'normal', 'high');
        add_meta_box('kwps_questions', 'Question', array('\includes\poll', 'display_questions_metabox'), 'kwps_poll', 'normal', 'high');
    }

    /**
     * @param $post
     */
    static function display_intro_and_outro_metabox($post) {
        wp_nonce_field( basename( __FILE__ ), 'kwps_nonce' );

        $intro = get_post_meta($post->ID, '_kwps_intro', true);
        $outro = get_post_meta($post->ID, '_kwps_outro', true);

        ?>
        <label for="_kwps_intro">Intro</label>
        <textarea name="_kwps_intro"><?php echo $intro; ?></textarea>

        <label for="_kwps_outro">Outro</label>
        <textarea name="_kwps_outro"><?php echo $outro; ?></textarea>

    <?php
    }

    /**
     * @param $post
     */
    static function display_questions_metabox($post){
        $saved_custom_fields = get_post_custom_keys($post->ID);

        $answer_options = array();

        foreach($saved_custom_fields as $custom_field){
            if (strpos($custom_field, '_kwps_answer_') !== false) {
                array_push($answer_options, $custom_field);
            }
        }
        ?>

        <label for="_kwps_question">Question</label>
        <textarea name="_kwps_question"><?php echo get_post_meta($post->ID, '_kwps_question', true); ?></textarea>

        <?php
        if(count($answer_options) < 2 ){
            $answer_options = array('_kwps_answer_1', '_kwps_answer_2');
        }
        foreach($answer_options as $key){
            $label = substr(strrchr($key, "_"), 1);
            ?>
            <label for="<?php echo $key; ?>">Answer <?php echo $label;?></label>
            <textarea name="<?php echo $key; ?>"><?php echo get_post_meta($post->ID, $key, true); ?></textarea>
        <?php
        }
    }

    /**
     * Saves the custom meta input
     */
    static function meta_save( $post_id ) {

        $allowdHtmlTags = array(
            'a' => array(
                'href' => array(),
                'title' => array()
            ),
            'br' => array(),
            'em' => array(),
            'strong' => array(),
            'h1' => array(),
            'p' => array(),
        );

        // Checks save status
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $_POST[ 'kwps_nonce' ] ) && wp_verify_nonce( $_POST[ 'kwps_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

        // Exits script depending on save status
        if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
            return;
        }

        // Checks for input and sanitizes/saves if needed
        if( isset( $_POST[ '_kwps_intro' ] ) ) {
            update_post_meta( $post_id, '_kwps_intro', wp_kses( $_POST[ '_kwps_intro' ], $allowdHtmlTags ) );
        }

        if( isset( $_POST[ '_kwps_outro' ] ) ) {
            update_post_meta( $post_id, '_kwps_outro', wp_kses( $_POST[ '_kwps_outro' ], $allowdHtmlTags ) );
        }

        if( isset( $_POST[ '_kwps_question' ] ) ) {
            update_post_meta( $post_id, '_kwps_question', wp_kses( $_POST[ '_kwps_question' ], $allowdHtmlTags ) );
        }

        $field_prefix = '_kwps_answer_';
        $saved_custom_fields = get_post_custom_keys($post_id);
        $form_fields = array_keys($_POST);

        if(is_array($saved_custom_fields)){
            foreach($saved_custom_fields as $custom_field){
                if(! in_array($custom_field, $form_fields)){
                    delete_post_meta($post_id, $custom_field);
                }
            }
        }

        foreach($form_fields as $form_field){
            if (strpos($form_field, $field_prefix) !== false) {
                update_post_meta( $post_id, $form_field, wp_kses( $_POST[ $form_field ], $allowdHtmlTags ) );
            }
        }

        $saved_view_count = get_post_meta($post_id, '_kwps_view_count', true);
        if(strlen($saved_view_count) == 0) {
            update_post_meta($post_id, '_kwps_view_count', 0);
        }
    }

    /**
     * This function displays the current poll as json
     * Can only be used inside the WP loop!
     *
     */
    static function display_poll_as_json(){
        global $post;

        $post_as_array = self::get_poll($post->ID);
        $answer_options = self::get_answer_options_of_poll($post->ID);
        $post_as_array['_kwps_answer_options'] = $answer_options;

        wp_send_json($post_as_array);
    }

    /**
     * This function returns the poll with the corresponding post_id as an associative array.
     * Returns null when no poll found.
     * Other versions of the poll are not retrieved.
     *
     * @param $post_id
     * @return null|array
     */
    static function get_poll($post_id){
        $post_as_array =get_post($post_id,ARRAY_A);

        $post_as_array['_kwps_intro'] = get_post_meta($post_id, '_kwps_intro', true);
        $post_as_array['_kwps_outro'] = get_post_meta($post_id, '_kwps_outro', true);
        $post_as_array['_kwps_question'] = get_post_meta($post_id, '_kwps_question', true);
        $post_as_array['_kwps_view_count'] = get_post_meta($post_id, '_kwps_view_count', true);

        return $post_as_array;
    }

    /**
     * @param $post_id
     * @return array|null
     */
    static function get_poll_with_versions($post_id) {

        $parent_poll = self::get_poll($post_id);

//    retrieve children of this post aka versions
        $versions_array = self::get_versions_of_poll($post_id);

        $parent_poll['versions'] = $versions_array;

        return $parent_poll;
    }

    /**
     * @param $post_id
     * @return array
     */
    static function get_versions_of_poll($post_id){
        $versions_as_objects = get_children(array('post_parent' => $post_id));
        $versions = array();

        foreach($versions_as_objects as $version_object){
            $version = self::get_poll($version_object->ID);
            array_push($versions, (array) $version);
        }

        return $versions;
    }

    /**
     * @param $post_id
     * @return array
     */
    static function get_answer_options_of_poll($post_id){
        $answer_options = get_post_meta($post_id, '_kwps_answers', true);

        $return_array = array();

        foreach($answer_options as $answer_option){
            $answer_object = array();
            $answer_object['postId'] = $post_id;
            $answer_object['answerOption'] = $answer_option;
            array_push($return_array, $answer_object);
        }

        return $return_array;
    }

    /**
     * @param $versions
     * @return array
     */
    static function get_answer_options_of_versions($versions){
        $answer_options = array();
        foreach($versions as $version){
            $answer_option = self::get_answer_options_of_poll($version['ID']);

            $answer_options = array_merge($answer_options, $answer_option);
        }

        return $answer_options;
    }

    /**
     *
     */
    static function save_poll(){
        if( self::validate_new_poll($_POST) ) {
            echo 'validated';
        }

        self::save_post($_POST);

        die();
    }

    /**
     * @param $post
     * @return bool
     */
    static function validate_new_poll($post) {
        $required_fields = array(
            'post_title',
            'post_status',
            'post_type',
            '_kwps_intro',
            '_kwps_outro',
            '_kwps_question',
            '_kwps_answers',
        );

        foreach($required_fields as $field)
            if(! isset($post[$field])) {
                return false;
            } else {
                if( is_string($post[$field])){
                    if( strlen($post[$field]) == 0 ) {
                        return false;
                    }
                }
            }
        return true;
    }

    /**
     * @param $post
     */
    static function save_post($post){
        $post_id = wp_insert_post($post);
        var_dump($post_id);

        if( $post_id != 0 ){
            foreach($post as $field => $value){
                if( strpos($field, 'kwps') ) {
                    echo 'trying to save field: ' . $field . "<br>";
                    if( update_post_meta($post_id, $field, $value) ){
                        echo 'saved ' . $field;
                    } else {
                        echo 'failed ' . $field;
                    }
                }
            }
        } else {
            echo 'post could not be saved';
        }
    }
}