<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 26/03/14
 * Time: 14:04
 */

namespace includes;


class poll {
    static function kwps_register_post_types(){
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

    static function kwps_add_metaboxes() {
        add_meta_box('kwps_intro_and_outro', 'Intro en Outro', array('\includes\poll', 'kwps_display_intro_and_outro_metabox'), 'kwps_poll', 'normal', 'high');
        add_meta_box('kwps_questions', 'Question', array('\includes\poll', 'kwps_display_questions_metabox'), 'kwps_poll', 'normal', 'high');
    }

    static function kwps_display_intro_and_outro_metabox($post) {
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

    static function kwps_display_questions_metabox($post){
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
    static function kwps_meta_save( $post_id ) {

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

        foreach($saved_custom_fields as $custom_field){
            if(! in_array($custom_field, $form_fields)){
                delete_post_meta($post_id, $custom_field);
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

} 