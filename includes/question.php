<?php
namespace includes;

require_once 'kwps_post_type.php';

class Question extends Kwps_Post_Type{

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
        $required_fields = array(
            'post_parent',
            '_kwps_sort_order',
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