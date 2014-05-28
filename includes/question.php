<?php
namespace includes;

require_once 'kwps_post_type.php';

class Question extends Kwps_Post_Type{

    public static $numeric_fields = array();

    public static $required_fields = array(
        'post_content',
        'post_parent',
        '_kwps_sort_order',
    );

    public static $post_type = 'kwps_question';

    public static $rewrite = array(
            'slug' => 'questions',
            'with_front' => false,
        );

    public static $post_type_args = array(
        'public' => false,
        'supports' => array('editor'),
        'labels' => array(
            'name' => 'Questions',
            'singular_name' => 'Question',
            'add_new' => 'Add New Question',
            'add_new_item' => 'Add New Question',
            'edit_item' => 'Edit Question',
            'new_item' => 'New Question',
            'view_item' => 'View Question',
            'search_items' => 'Search Questions',
            'not_found' => 'No Questions Found',
            'not_found_in_trash' => 'No Questions Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => true,
        'hierarchical' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
    );

    public static function get_test_modus($question_id)
    {
        $question = static::get_as_array($question_id);
        return Question_Group::get_test_modus($question['post_parent']);
    }

    public static function get_version($question_id){
        $question = static::get_as_array($question_id);
        return Question_Group::get_version($question['post_parent']);
    }

    public static function get_count_per_version($version_id) {
        $question_groups = Question_Group::get_all_by_post_parent($version_id);

        $count_per_version = 0;

        foreach( $question_groups as $question_group ) {
            $questions = Question::get_all_by_post_parent($question_group['ID']);
            $count_per_version = $count_per_version + sizeof($questions);
        }

        return $count_per_version;
    }

    public static function get_html($question_id){
        $question = static::get_as_array($question_id);

        return '<div class="kwps-question">' . $question['post_content'] . '</div>';
    }

    public static function validate_for_delete($question_id = 0)
    {
/*        A question is not allowed to be deleted when:
            - The test collection is not in draft anymore
*/
        $question_group_id = wp_get_post_parent_id($question_id);
        $version_id = wp_get_post_parent_id($question_group_id);
        $test_collection_id = wp_get_post_parent_id($version_id);

        $test_status = get_post_status($test_collection_id);

        if('draft' != $test_status){
            return false;
        } else {
            return true;
        }
    }


    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_insert($post_as_array = array()) {
        $errors = static::check_required_fields($post_as_array);
        $errors = array_merge($errors, static::check_numeric_fields($post_as_array));
        $errors = array_merge($errors, static::check_max_questions_per_question_group($post_as_array));

        return $errors;
    }

    private static function check_max_questions_per_question_group($post){
        $errors = array();

        if( isset( $post['post_parent'] ) ){
            $question_group = Question_Group::get_as_array($post['post_parent']);
            $test_modus = Question_Group::get_test_modus($question_group['ID']);

            $all_questions_of_same_group = Question::get_all_by_post_parent($post['post_parent']);

            $max_questions_per_question_group = (int) $test_modus['_kwps_max_questions_per_question_group'];

            if( 0 < $max_questions_per_question_group ){
                if( sizeof($all_questions_of_same_group) >= $test_modus['_kwps_max_questions_per_question_group']){
                    array_push( $errors, array( 'field' => 'All', 'message' => 'Maximum questions already reached' ) );
                }
            }
        }

        return $errors;
    }

    /**
     * @param $post_as_array
     * @return bool
     */
    static function validate_for_update($post_as_array = array()) {
        $required_fields = array(
            'ID',
            'post_content',
            'post_parent'
        );

        foreach($required_fields as $field)
            if(! isset($post_as_array[$field])) {
                return false;
            } else {
                if( is_string($post_as_array[$field])){
                    if( strlen($post_as_array[$field]) == 0 ) {
                        return false;
                    }
                }
            }
        return true;
    }
}