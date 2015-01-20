<?php

namespace kwps_classes;


class Pie_Chart extends Bar_Chart
{
    public static function get_chart_per_question($entry_id, $group) {
	    $version = Entry::get_version($entry_id);

        return static::get_chart_per_question_per_version( $version['ID'], $group);

    }

    public static function get_chart_per_question_per_version( $version_id, $group ) {
        /* QUESTION GROUP DATA */
        $question_group = Question_Group::get_one_by_post_parent( $version_id );

        /* QUESTION DATA */
        $question = Question::get_one_by_post_parent($question_group['ID']);

        /* ANSWER OPTIONS DATA */
        $answer_options = Answer_Option::get_all_by_post_parent($question['ID']);

        /* ENTRY DATA */
        $total_entries = 0;
        $entry_totals_per_answer_option = array();
        $answer_option_contents = array();

        foreach($answer_options as $answer_option){
            if( strlen( $group ) > 0 ) {
                $entries = Entry::get_all_of_result_group($answer_option['ID'], $group);
            } else {
                $entries = Entry::get_all_by_post_parent($answer_option['ID']);
            }

            $entry_totals_per_answer_option[] =
                array( $answer_option['post_content'] ,sizeof($entries) );
            $answer_option_contents[] = $answer_option['post_content'];
            $total_entries += sizeof($entries);
        }

        $pie_data = array();

        foreach($entry_totals_per_answer_option as $answer_option_info){
            $percentage = $answer_option_info[1]/$total_entries*100;
            $pie_data[] = array( $answer_option_info[0] , $percentage );
        }
        // samenvoegen van answer option met percentage, array per regel
        // [['answer option', 'percentage'], ['answer option', 'percentage'], ...]


        // waar gaat version naartoe?
//        $version = Entry::get_version($request_data['ID']);
//            $pie_chart = static::get_chart($question, $pie_data);
        $data = array( $question, $pie_data );
        $pie_chart = static::get_chart($data);

        return $pie_chart;
    }

    public static function get_chart($data) {
        $question = $data[0];
        $pie_data = $data[1];
        return array(
            'chart' => array( 'plotBackgroundColor' => null, 'plotBorderWidth' => null, 'plotShadow' => false ),
            'title' => array( 'text' => $question['post_content'] ),
            'tooltip' => array( 'pointFormat' => '{series.name}: <b>{point.percentage:.1f}%</b>'),
            'plotOptions' => array( 'pie' => array( 'allowPointSelect' => true, 'cursor' => 'pointer', 'dataLabels' => array( 'enabled' => true, 'format' => '<b>{point.name}</b>: {point.percentage:.1f} %', 'style' => array( 'color' => 'black') ) ) ),
            
            'exporting' => array( 'enabled' => false ),
            'legend' => array( 'enabled' => false ),
            'credits' => array( 'enabled' => false ),
            'series' => array(array( 'type' => 'pie','name' => 'Votes', 'data' => $pie_data)),
        );
    }
} 