<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 13/05/14
 * Time: 11:32
 */

namespace kwps_classes;


class Question_Group extends Kwps_Post_Type {

    public static $numeric_fields = array('_kwps_sort_order');

    public static $required_fields = array(
        'post_parent',
        '_kwps_sort_order',
    );

    public static $form_fields = array(
        'ID',
        'post_title',
        'post_content',
        'post_parent',
        'post_status',
        '_kwps_sort_order',
    );

    public static $additional_validation_methods = array(
        'check_max_question_groups',
    );

    public static $meta_data_fields = array('_kwps_sort_order');

    public static $post_type = 'kwps_question_group';

    public static $rewrite = array(
        'slug' => 'question_groups',
        'with_front' => false,
    );

    public static $post_type_args = array(
        'public' => true,
        'supports' => array(
            'title',
        ),
        'labels' => array(
            'name' => 'Question Groups',
            'singular_name' => 'Question Group',
            'add_new' => 'Add New Question Group',
            'add_new_item' => 'Add New Question Group',
            'edit_item' => 'Edit Question Group',
            'new_item' => 'New Question Group',
            'view_item' => 'View Question Group',
            'search_items' => 'Search Question Groups',
            'not_found' => 'No Question Groups Found',
            'not_found_in_trash' => 'No Question Groups Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
    );

    public static function get_test_modus($question_group_id)
    {
        $question_group = static::get_as_array($question_group_id);
        return Version::get_test_modus($question_group['post_parent']);
    }

    public static function get_version($question_group_id){
        $question_group = static::get_as_array($question_group_id);
        return Version::get_as_array($question_group['post_parent']);
    }


    public static function get_html($id)
    {
        // TODO: Implement get_html() method.
        return '';
    }

    public static function check_max_question_groups($post){
        $errors = array();

        if( isset( $post['post_parent'] ) ){
            $version = Version::get_as_array($post['post_parent']);
            $test_modus = Version::get_test_modus($version['ID']);

            $kwps_max_question_groups = $test_modus['_kwps_max_question_groups'];

            if( ( !isset( $post['ID'] ) ) && 0 < $kwps_max_question_groups ){
                $all_question_groups_of_version = Question_Group::get_all_by_post_parent($version['ID']);

                if( sizeof($all_question_groups_of_version) >= $kwps_max_question_groups){
                    array_push( $errors,
                        array(
                            'field' => 'All',
                            'message' => __( 'Maximum question groups already reached', 'klasse-wp-poll-survey' ),
                        )
                    );
                }
            }
        }
        return $errors;
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
        return true;
    }


} 