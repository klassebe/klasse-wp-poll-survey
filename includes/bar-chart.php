<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 4/06/14
 * Time: 15:40
 */

namespace includes;


class Bar_Chart {

    public static function ajax_get_chart_per_question_by_entry_id(){
        $request_data = static::get_post_data_from_request();

        $errors = array();
        if( ! isset($request_data['ID']) ) {
            $errors[] = array('field' => 'ID', 'Required');
        }

        static::process_get_request($request_data, $errors);
    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);

        return $request_data;
    }

    public static function process_get_request($request_data, $errors){
        if( sizeof( $errors ) > 0 ) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
            wp_send_json_error($errors);
        } else {
            $entry = Entry::get_as_array($request_data['ID']);
            $answer_option = Answer_Option::get_as_array( $entry['post_parent'] );

            $question = Question::get_as_array( $answer_option['post_parent'] );
            $answer_options = Answer_Option::get_all_by_post_parent($question['ID']);

            $total_entries = 0;

            $entry_totals_per_answer_option = array();
            $answer_option_contents = array();

            foreach($answer_options as $answer_option){
                $entries = Entry::get_all_by_post_parent($answer_option['ID']);
                $entry_totals_per_answer_option[$answer_option['ID']] = sizeof($entries);
                $answer_option_contents[] = $answer_option['post_content'];
                $total_entries += sizeof($entries);
            }

            $percentages = array();

            foreach($entry_totals_per_answer_option as $id => $count){
                $percentages[] = $count/$total_entries*100;
            }

            $version = Entry::get_version($request_data['ID']);
            $bar_chart = static::get_chart($question, $answer_option_contents, $percentages);
            wp_send_json( $bar_chart );
        }

        die();
    }

    public static function get_chart($question, $answer_options, $percentages){
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
            'series' => array( 'name' => 'Votes', 'data' => $percentages),
        );
    }
} 