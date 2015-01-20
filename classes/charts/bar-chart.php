<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 4/06/14
 * Time: 15:40
 */

namespace kwps_classes;


class Bar_Chart 
{
    public static function get_chart_per_test_collection( $test_collection_id, $group ) {
        $versions = Version::get_all_by_post_parent( $test_collection_id );

        foreach( $versions as $version ) {
            /* QUESTION GROUP DATA */
            $question_group = Question_Group::get_one_by_post_parent($version['ID']);

            /* QUESTION DATA */
            $question = Question::get_one_by_post_parent($question_group['ID']);

            /* ANSWER OPTIONS DATA */
            $answer_options = Answer_Option::get_all_by_post_parent($question['ID']);

            /* ENTRY DATA */
            $total_entries = 0;
            $entry_totals_per_answer_option = array();
            $answer_option_contents = array();

            foreach ($answer_options as $answer_option) {
                if( strlen( $group ) > 0 ) {
                    $entries = Entry::get_all_of_result_group($answer_option['ID'], $group);
                } else {
                    $entries = Entry::get_all_by_post_parent($answer_option['ID']);
                }

                $entry_totals_per_answer_option[$answer_option['ID']] = sizeof($entries);
                $answer_option_contents[] = $answer_option['post_content'];
                $total_entries += sizeof($entries);
            }

            $percentages = array();

            foreach ($entry_totals_per_answer_option as $id => $count) {
                // check if total_entries is not 0!
                if ($total_entries !== 0) {
                    $percentages[] = $count/$total_entries*100;
                } else {
                    $percentages[] = 0;
                }
            }

            $data = array( $question, $answer_option_contents, $percentages );
            $bar_chart = static::get_consolidated_chart( $data );

            return $bar_chart;
        }
    }

    public static function get_chart_per_question_per_version( $version_id, $group ) {
        /* QUESTION GROUP DATA */
        $question_group = Question_Group::get_one_by_post_parent($version_id);

        /* QUESTION DATA */
        $question = Question::get_one_by_post_parent($question_group['ID']);

        /* ANSWER OPTIONS DATA */
        $answer_options = Answer_Option::get_all_by_post_parent($question['ID']);

        /* ENTRY DATA */
        $total_entries = 0;
        $entry_totals_per_answer_option = array();
        $answer_option_contents = array();

        foreach ($answer_options as $answer_option) {
            if( strlen( $group ) > 0 ) {
                $entries = Entry::get_all_of_result_group($answer_option['ID'], $group);
            } else {
                $entries = Entry::get_all_by_post_parent($answer_option['ID']);
            }

            $entry_totals_per_answer_option[$answer_option['ID']] = sizeof($entries);
            $answer_option_contents[] = $answer_option['post_content'];
            $total_entries += sizeof($entries);
        }

        $percentages = array();

        foreach ($entry_totals_per_answer_option as $id => $count) {
            // check if total_entries is not 0!
            if ($total_entries !== 0) {
                $percentages[] = $count/$total_entries*100;
            } else {
                $percentages[] = 0;
            }
        }

        $data = array( $question, $answer_option_contents, $percentages );
        $bar_chart = static::get_chart($data);

        return $bar_chart;
    }

    public static function get_chart_per_question_by_entry_id($entry_id, $group){
        $version = Entry::get_version($entry_id);
        return static::get_chart_per_question_per_version( $version['ID'], $group);

    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);

        return $request_data;
    }

    public static function get_chart($data){
        $question = $data[0];
        $answer_options = $data[1];
        $percentages = $data[2];
        return array(
            'chart' => array( 'type' => 'bar' ),
            'title' => array( 'text' => $question['post_content'] ),
            'xAxis' => array(
                                'categories' => $answer_options,
                                'title' => array( 'text' => null ),
                        ),
            'yAxis' => array(
                            'max' => 100,
                            'min' => 0,
                            'title' => array( 'text' => 'percent', 'align' => 'high'),
                            'labels' => array( 'overflow' => 'justify' ),
                        ),
            'tooltip' => array( 'valueSuffix' => ' %'),
            'plotOptions' => array( 'bar' => array( 'dataLabels' => array('enabled' => true) ) ),
            'exporting' => array( 'enabled' => false ),
            'legend' => array( 'enabled' => false ),
            'credits' => array( 'enabled' => false ),
            'series' => array(array( 'name' => 'Votes', 'data' => $percentages)),
        );
    }

    public static function get_consolidated_chart( $data ) {

    }
} 