<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 11/05/14
 * Time: 17:48
 */

namespace kwps_classes;

require_once 'kwps-post-type.php';


class Test_Collection extends Kwps_Post_Type{

    public static $numeric_fields = array(
        '_kwps_sort_order',
	    '_kwps_show_grouping_form'
    );

    public static $required_fields = array(
            'post_status',
            'post_parent',
            '_kwps_sort_order',
            '_kwps_logged_in_user_limit',
            '_kwps_logged_out_user_limit',
        );

    public static $additional_validation_methods = array(
        'check_allowed_dropdown_values',
    );

    public static $post_type = 'kwps_test_collection';

    public static $rewrite = array(
        'slug' => 'test_collections',
        'with_front' => false,
    );

    public static $allowed_dropdown_values = array(
        '_kwps_logged_in_user_limit' => array(
            'free',
            'cookie',
            'ip',
            'once',
        ),
        '_kwps_logged_out_user_limit' => array(
            'free',
            'cookie',
            'ip',
            'none',
        ),
    );

    public static $meta_data_fields = array(
        '_kwps_logged_in_user_limit',
        '_kwps_logged_out_user_limit',
        '_kwps_sort_order',
        '_kwps_show_grouping_form',
    );

    public static $post_type_args = array(
        'public' => true,
        'supports' => array(
            'title',
        ),
        'labels' => array(
            'name' => 'Test Collections',
            'singular_name' => 'Test Collection',
            'add_new' => 'Add New Test Collection',
            'add_new_item' => 'Add New Test Collection',
            'edit_item' => 'Edit Test Collection',
            'new_item' => 'New Test Collection',
            'view_item' => 'View Test Collection',
            'search_items' => 'Search Test Collections',
            'not_found' => 'No Test Collections Found',
            'not_found_in_trash' => 'No Test Collections Found In Trash',
        ),
        'show_in_menu' => false,
        'show_ui' => false,
        'hierarchical' => true,
    );

    public static function shortcode($atts){
        extract( shortcode_atts( array(
            'id' => 0,
            'version' => 'all',
        ), $atts ) );

        return static::get_html($id);
    }

    public static function get_html($id)
    {
        $output = '';

        $url_parameters = $_GET;

        $test_collection = static::get_as_array($id);
        $test_collection_outro = Coll_Outro::get_one_by_post_parent($id);

        if( $test_collection['_kwps_show_grouping_form'] == 0 ) {
            $output .= '<div class="kwps-error">' ;
            $output .= __('Shortcode cannot be displayed, incorrect settings for Test Collection', 'klasse-wp-poll-survey');
            $output .= '</div>';
        } else {
            $versions = Version::get_all_by_post_parent($id);

            $bits = 50;
            $group_hash = bin2hex(openssl_random_pseudo_bytes($bits));
            $result_hash = bin2hex(openssl_random_pseudo_bytes($bits));

            if( !isset( $url_parameters['version'] ) && !isset( $url_parameters['_kwps_group'] ) && !isset( $url_parameters['_kwps_result_hash'] ) ) {
                $output .= '<input type="hidden" class="admin-url" value="' .  admin_url() . '">';
                $output .= '<div class="kwps-test-collection">';
                $output .= '<div class="kwps-page kwps-grouping-form">';
                $output .= '<input id="kwps-result-group" type="text" name="post_title"/>';
                $output .= '<input type="hidden" name="_kwps_hash" value="' . $group_hash . '" />';
                $output .= '<input type="hidden" name="_kwps_result_hash" value="' . $result_hash . '" />';
                $output .= '<input type="hidden" name="post_parent" value="' . $id . '" />';
                $output .= '<div class="kwps-button">';
                $output .= '<button class="kwps-next">';
                $output .= __('Next', 'klasse-wp-poll-survey');
                $output .= '</button>';
                $output .= '</div>'; // closes div class kwps-button
                $output .= '</div>'; // closes div class kwps-page-grouping-form

                $output .= '<div class="kwps-page kwps-grouping-urls">';
                foreach( $versions as $version ) {
                    $params = array(
                        'version' => $version['ID'],
                        '_kwps_group' => $group_hash,
                    );
                    $url = add_query_arg( $params, get_permalink() );

                    $output .= '<a href="' . $url . '">' . $version['post_title'] .'</a>' ;
                }

                $output .= '<div class="kwps-result-url">';
                $params = array(
                    'test_collection' => $test_collection['ID'],
                    '_kwps_result_hash' => $result_hash,
                );
                $url = add_query_arg( $params, get_permalink() );
                $output .= '<a href="' . $url . '">Results</a>';
                $output .= '</div>'; // closes div class kwps-result-url
	            $output .= '</div>'; // closes div class kwps-page-grouping-urls

                $output .= '</div>'; // closes div class kwps-test-collection
            } elseif( isset( $url_parameters['version'] ) && isset( $url_parameters['_kwps_group'] ) ) {
                if( Result_Group::is_valid_hash( $test_collection['ID'], $url_parameters['_kwps_group'] ) ) {
                    $output .= Version::get_html($url_parameters['version']);
                } else {
                    $output .= '<div class="kwps-error">' ;
                    $output .= __('Shortcode cannot be displayed due to incorrect hash', 'klasse-wp-poll-survey');
                    $output .= '</div>';
                }
            } elseif( isset( $url_parameters['test_collection'] ) && isset( $url_parameters['_kwps_result_hash'] ) ) {
	            $output .= '<input type="hidden" class="admin-url" value="' .  admin_url() . '">';
	            $output .= '<div class="kwps-coll-outro">';
                $output .= '<div class="kwps-content">';

                /* SEARCH THE SHORTCODE AND REPLACE IT */
                $replacement_arr = [];
                $pattern_arr = [];
                $pattern = '/\[kwps_result.*\]/';
                $subject = $test_collection_outro['post_content'];
                preg_match_all($pattern, $subject, $kwps_result_matches);
                foreach ($kwps_result_matches[0] as $kwps_result_match) {
                    $replacement_arr[] = do_shortcode($kwps_result_match);
                    $pattern_arr[] = '/\\' . substr($kwps_result_match,0,-1) . '\]/';
                }
                $output .= preg_replace($pattern_arr, $replacement_arr, $subject);

                $output .= '</div>'; // closes div class kwps-content
                $output .= '</div>'; // closes div class kwps-coll-outro

            } else {
                $output .= '<div class="kwps-error">' ;
                $output .= __('Shortcode cannot be displayed due to incorrect request', 'klasse-wp-poll-survey');
                $output .= '</div>';

            }
        }

        return $output;
    }

        public static function get_test_modus($test_collection_id)
    {
        $test_collection = static::get_as_array($test_collection_id);
        return Test_Modus::get_as_array($test_collection['post_parent']);
    }

    public static function get_view_count($test_collection_id){
        $view_count_total = 0;
        $versions = Version::get_all_by_post_parent($test_collection_id);

        foreach($versions as $version){
            $view_count = (int) $version['_kwps_view_count'];
            $view_count_total = $view_count_total + $view_count;
        }

        return $view_count_total;
    }

    public static function check_allowed_dropdown_values($post){
        $errors = array();
        foreach( static::$allowed_dropdown_values as $field => $allowed_values ){
            if( isset( $post[$field] ) ) {
                if( !in_array( $post[$field], $allowed_values) ) {
                    array_push( $errors ,
                        array(
                            'field' => $field,
                            'message' => __( 'Value is not allowed', 'klasse-wp-poll-survey' ),
                        )
                    );
                }
            }
        }
        return $errors;
    }

    public static function ajax_validate_for_publish(){
        $test_collection = static::get_post_data_from_request();
        $response = static::validate_for_publish($test_collection);

        if( sizeof( $response ) > 0 ) {
            wp_send_json_error($response);
        } else {
            wp_send_json_success($response);
        }

        die();
    }

    public static function validate_for_publish($test_collection){
        $versions = Version::get_all_by_post_parent($test_collection['ID']);

        $errors = array();

        foreach($versions as $version){
            $version_errors = Version::validate_for_publish($version);
            if( sizeof( $version_errors) > 0) {
                $errors[] = $version_errors;
            }
        }

        if( sizeof( $errors ) != 0 ) {
            foreach($versions as $version){
                $version['post_status'] = 'publish';
                Version::save_post($version);
            }
        }

        return $errors;
    }

    public static function validate_for_delete($post_id = 0)
    {
        // TODO: Implement validate_for_delete() method.
    }
}