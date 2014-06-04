<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 23/05/14
 * Time: 09:24
 */

namespace includes;


class Result {
    public static function get_result_of_version_by_entry_id(){
        $request_data = static::get_post_data_from_request();

        $version_id = $request_data['ID'];
        $output_type = $request_data['output_type'];


        switch($output_type){
            case 'bar-chart-per-question' :
                $results = static::bar_chart_per_question($version_id);
                break;
            case 'pie-chart-per-question' :
                $results = static::bar_chart_per_question($version_id);
                break;
            case 'stacked-bar-per-question' :
                $results = static::bar_chart_per_question($version_id);
                break;
        }
        wp_send_json( $results );
        die;
    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);

        return $request_data;
    }

    public static function bar_chart_per_question($entry_id) {
        $version= Entry::get_version($entry_id);

        $question_groups = Question_Group::get_all_by_post_parent($version['ID']);
        $results = array();

        foreach($question_groups as $question_group){
            $questions = Question::get_all_by_post_parent( $question_group['ID'] );

            foreach($questions as $question){
                array_push($results, static::get_results_by_question( $question['ID'] ) );
            }
        }
        return $results;
    }

    public static function get_results_by_question($question_id){
        $args = array(
            'post_parent' => $question_id,
            'post_type'   => 'kwps_answer_option',
            'posts_per_page' => -1,
            'post_status' => 'any',
        );

        $answer_options = get_children($args, ARRAY_A);

        $results = array('entries' => array());
        $totalEntries = 0;

        foreach ($answer_options as $answer_option) {
            $entries = Entry::get_all_by_post_parent($answer_option['ID']);

            array_push($results['entries'], array('answer_option_id' => $answer_option['ID'],
                'answer_option_content' => $answer_option['post_content'],'entry_count' => count($entries)));
            $totalEntries += count($entries);
        }

        $content_post = get_post($question_id);
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);

        $question = $content;

        array_push($results, array( 'total_entries' => $totalEntries));
        array_push($results, array( 'question' => $question));
        return $results;
    }
    public static function ajax_get_result_data_of_test_collection(){
        $request_data = static::get_post_data_from_request();
        $test_collection_id = $request_data['test_collection_id'];

        $results = static::get_test_collection_results( $test_collection_id );
        wp_send_json( $results );

    }

    public static function get_test_collection_results( $test_collection_id ){
        $results = array();

        $versions = Version::get_all_by_post_parent($test_collection_id);

        foreach($versions as $version) {
            $version_result = static::get_version_results($version);
            array_push( $results, $version_result );
        }

        return $results;
    }

    public static function get_version_results( $version) {
        $participants_count = static::get_participants_count_of_version( $version['ID'] );
        $conversion_rate = static::get_conversion_rate_of_version( $version );

        return array(
            'ID' => $version['ID'],
            'total_participants' => $participants_count,
            'conversion_rate' => $conversion_rate,
        );

    }

    public static function get_participants_count_of_test_collection( $test_collection_id ){
        $total_participants = array();
        $versions = Version::get_all_by_post_parent($test_collection_id);

        foreach($versions as $version){
            $participants_count = static::get_participants_count_of_version( $version['ID'] );
            array_push( $total_participants,
                array('ID' => $version['ID'], 'total_participants' => $participants_count ) );
        }
        return $total_participants;
    }

    public static function get_participants_count_of_version( $version_id ) {
        $participants_count = 0;
        $user_hashes = Entry::get_all_user_hashes_per_version( $version_id );

        foreach($user_hashes as $user_hash){

            $entries = Entry::get_all_by_user_hash_and_version( $user_hash, $version_id );

            foreach($entries as $entry){
                if (Entry::is_part_of_completed_test( $entry['ID'] ) ){
                    $participants_count++;
                }
            }
        }
        return $participants_count;
    }

    public static function get_conversion_rate_of_test_collection( $test_collection_id ){
        $conversion_rates = array();
        $versions = Version::get_all_by_post_parent($test_collection_id);

        foreach($versions as $version){
            $conversion_rate = static::get_conversion_rate_of_version($version);

            array_push( $conversion_rates,
                array('ID' => $version['ID'], 'conversion_rate' => $conversion_rate ) );
        }

        return $conversion_rates;
    }

    public static function get_conversion_rate_of_version( $version ){
        $total_participants = static::get_participants_count_of_version( $version['ID'] );
        $view_count = (int) $version['_kwps_view_count'];
        if( $view_count > 0 ){
            $conversion_rate = $total_participants / $view_count;
        } else {
            $conversion_rate = 0;
        }

        return $conversion_rate;
    }
    public static function shortcode( $atts ) {
        extract( shortcode_atts( array(
            'result' => 0,
        ), $atts ) );

        return '<div class="kwps-result ' . $result . '"> ' . $result . '</div>';
    }
}
/* EOF */