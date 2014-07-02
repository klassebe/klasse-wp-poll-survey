<?php

namespace includes;

require_once 'kwps-post-type.php';

/**
 * Class Answer_Option
 * @package includes
 *
 *
 */
class Answer_Option extends Kwps_Post_Type{

    /**
     * @var array contains all meta data keys for which the value should be a number
     */
    public static $numeric_fields = array(
        '_kwps_sort_order',
        '_kwps_answer_option_value',
    );

    /**
     * @var array contains all meta data keys for which a value is required
     */
    public static $required_fields = array(
        'post_content',
        'post_parent',
        '_kwps_sort_order',
    );

    /**
     * @var array contains names of all the addional validation methods that need to run
     */
    public static $additional_validation_methods = array(
        'check_max_answer_options_per_question',
        'check_kwps_answer_option_value_required'
    );

    /**
     * @var array contains all meta data keys
     */
    public static $meta_data_fields = array(
        '_kwps_sort_order',
        '_kwps_answer_option_value',
    );

    /**
     * @var string slug of the post type, used to register the post type
     */
    public static $post_type = 'kwps_answer_option';

    /**
     * @var array contains the settings for the rewrite rules when registering the post type
     */
    public static $rewrite = array(
            'slug' => 'answer_options',
            'with_front' => false,
        );

    /**
     * @var array contains all arguments to register the post type
     */
    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Answer Options',
            'singular_name' => 'Answer',
            'add_new' => 'Add New Answer',
            'add_new_item' => 'Add New Answer',
            'edit_item' => 'Edit Answer',
            'new_item' => 'New Answer',
            'view_item' => 'View Answer',
            'search_items' => 'Search Answers',
            'not_found' => 'No Answers Found',
            'not_found_in_trash' => 'No Answers Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    /**
     * Returns the test modus, as an associative array, to which the answer_option belongs
     *
     * @param $answer_option_id
     * @return array|bool|null|\WP_Post
     */
    public static function get_test_modus($answer_option_id)
    {
        $answer_option = static::get_as_array($answer_option_id);
        return Question::get_test_modus($answer_option['post_parent']);
    }

    /**
     * Returns the version, as an associative array, to which the answer_option belongs
     *
     * @param $answer_option_id
     * @return array|bool|null|\WP_Post
     */
    public static function get_version($answer_option_id){
        $answer_option = static::get_as_array($answer_option_id);
        return Question::get_version($answer_option['post_parent']);
    }

    /**
     * Returns the sort order of the answer option
     *
     * @param $answer_option_id
     * @return mixed
     */
    public static function get_sort_order($answer_option_id){
        return get_post_meta($answer_option_id, '_kwps_sort_order', true);
    }

    /**
     * Returns a string containing the html to display an answer option
     *
     * @param $answer_option_id
     * @return string
     */
    public static function get_html($answer_option_id){
        $answer_option = static::get_as_array($answer_option_id);

        $dump = '<div class="kwps-single-answer kwps-answer-' . static::get_sort_order($answer_option_id) . '">';
        $dump .= '<input type="radio" name="kwps-answer"';
        $dump .= $answer_option['post_parent'];
        $dump .= ' value="'. $answer_option['ID'] .'">'. $answer_option['post_content'] . '</div>';

        return $dump;
    }

    /**
     * Returns true when allowed to delete the answer option, false when not
     *
     * @param int $answer_option_id
     * @return bool
     */
    public static function validate_for_delete($answer_option_id = 0)
    {
        $question_id = wp_get_post_parent_id($answer_option_id);
        return Question::validate_for_delete($question_id);
    }

    /**
     * Returns a string containing the html to display all answer options that belong to the question
     *
     * @param $question_id
     * @return string
     */
    public static function get_all_html($question_id)
    {
        $i = 0;
        $dump = '';
        foreach(static::get_all_by_post_parent($question_id) as $answer_option){
            $dump .= static::get_html($answer_option['ID']);
        }

        return $dump;
    }

    /**
     * Return an associative array containing an error message in case the number of answer options for the question
     * reaches the maximum allowed answer options per question
     *
     * @param array $answer_option Associative array with all answer option fields as keys
     * @return array
     */
    public static function check_max_answer_options_per_question($answer_option){
        $errors = array();

        if( isset( $answer_option['post_parent'] ) ){
            $question = Question::get_as_array($answer_option['post_parent']);
            $test_modus = Question::get_test_modus($question['ID']);

            $all_answer_options_of_same_question = Answer_Option::get_all_by_post_parent($answer_option['post_parent']);

            if( ( !isset( $post['ID'] ) ) &&  0 < $test_modus['_kwps_max_answer_options_per_question'] ){
                if( sizeof($all_answer_options_of_same_question) >= $test_modus['_kwps_max_answer_options_per_question']){
                    array_push( $errors,
                        array( 'field' => 'All',
                            'message' => __( 'Maximum answer options already reached', 'klasse-wp-poll-survey' ),
                        )
                    );
                }
            }
        }

        return $errors;
    }

    /**
     * Return an associative array containing error messages in case the answer option value fails validation
     *
     * @param array $answer_option Associative array with all answer option fields as keys
     * @return array
     */
    public static function check_kwps_answer_option_value_required($answer_option){
        $test_modus = Question::get_test_modus($answer_option['post_parent']);
        $errors = array();

        if( $test_modus['_kwps_answer_options_require_value'] > 0 ) {

            if(! isset($answer_option['_kwps_answer_option_value'])) {
                array_push($errors,
                    array(
                        'field' => '_kwps_answer_option_value',
                        'message' => __( 'Required', 'klasse-wp-poll-survey')
                    )
                );
            } else {
                if( is_string($answer_option['_kwps_answer_option_value'])){
                    if( strlen($answer_option['_kwps_answer_option_value']) == 0 ) {
                        array_push($errors,
                            array(
                                'field' => '_kwps_answer_option_value',
                                'message' => __( 'Required', 'klasse-wp-poll-survey')
                            )
                        );
                    }
                }
            }

            if( isset( $answer_option['_kwps_answer_option_value']) ) {
                if(! is_numeric( $answer_option['_kwps_answer_option_value'] ) ){
                    array_push( $errors ,
                        array(
                            'field' => '_kwps_answer_option_value',
                            'message' => 'Needs to be a number'
                        )
                    );
                }
            }
        }

        return $errors;
    }
}