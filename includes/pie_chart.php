<?php

namespace includes;


class Pie_Chart extends Bar_Chart
{

    public static function process_get_request($request_data, $errors) {
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
            $version = Entry::get_version($request_data['ID']);
//            $pie_chart = static::get_chart($question, $pie_data);
            $data = array( $question, $pie_data );
            $pie_chart = static::get_chart($data);
            wp_send_json( $pie_chart );
        }

        die();
    }

    public static function get_chart($data) {
        $question = $data[0];
        $pie_data = $data[1];
        return array(
            'chart' => array( 'plotBackgroundColor' => null, 'plotBorderWidth' => null, 'plotShadow' => false ),
            'title' => array( 'text' => $question['post_content'] ),
            'tooltip' => array( 'pointFormat' => '{series.name}: <b>{point.percentage:.1f}%</b>'),
            'plotOptions' => array( 'pie' => array( 'allowPointSelect' => true, 'cursor' => 'pointer', 'dataLabels' => array( 'enabled' => true, 'format' => '<b>{point.name}</b>: {point.percentage:.1f} %', 'style' => array( 'color' => "(Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'") ) ) ),
            
            'exporting' => array( 'enabled' => false ),
            'legend' => array( 'enabled' => false ),
            'credits' => array( 'enabled' => false ),
            'series' => array(array( 'type' => 'pie','name' => 'Votes', 'data' => $pie_data)),
        );
    }
} 