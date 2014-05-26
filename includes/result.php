<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 23/05/14
 * Time: 09:24
 */

namespace includes;


class Result {
    public static function get_result_of_version(){
        $request_data = static::get_post_data_from_request();

        $version_id = $request_data['ID'];
        $output_type = $request_data['output_type'];


        switch($output_type){
            case 'bar-chart-per-question' :
                $results = static::bar_chart_per_question($version_id);
                break;
        }
        var_dump($results);
        wp_send_json( $results );
        die;
    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);

        return $request_data;
    }

    public static function bar_chart_per_question($entry_id) {
        $version_id = Entry::get_version($entry_id);
        $question_groups = Question_Group::get_all_by_post_parent($version_id);
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

    public static function get_result_data_for_test_collection($test_collection_id){
        $versions = Version::get_all_by_post_parent($test_collection_id);

        foreach($versions as $version){

        }

    }
}