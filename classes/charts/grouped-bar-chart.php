<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 4/06/14
 * Time: 15:40
 */

namespace kwps_classes;


class Grouped_Bar_Chart
{

    public static function get_chart_per_profile($test_collection_id, $result_hash){
        $result_group = Result_Group::get_by_result_hash( $result_hash );
        if( ! $result_group) {
            return 0;
        }

        $test_collection = Test_Collection::get_as_array($test_collection_id);
        $first_version = Version::get_one_by_post_parent($test_collection_id);

        $versions = Version::get_all_by_post_parent($test_collection_id);

        $result_profiles_of_first_version = Result_Profile::get_all_by_post_parent($first_version['ID']);

        $titles = array();
        $grouped_result_profiles = array();

        foreach( $result_profiles_of_first_version as $result_profile ) {
            $title = $result_profile['post_title'] . '<br>';
            $result_profile_ids = array( $result_profile['ID'] );

            foreach( $versions as $version ) {
                if( $first_version['ID'] != $version['ID'] ) {
                    $matching_result_profile = Result_Profile::get_result_profile_by_version_and_min_max_value(
                        $version['ID'] , $result_profile['_kwps_min_value'], $result_profile['_kwps_max_value']
                    );

                    $title .= $matching_result_profile['post_title'] . '<br>';
                    $result_profile_ids[] = $matching_result_profile['ID'];
                }
            }

            $titles[] = $title;
            $grouped_result_profiles[] = $result_profile_ids;
        }

        $titles[] = 'No result profile';
        $grouped_result_profiles[] = array(-1);

        $versions_data = array();

        foreach( $versions as $version ) {

            $version_totals = array_fill(0, sizeof(  $grouped_result_profiles ), 0 );
            $user_hashes = Entry::get_all_user_hashes_per_version( $version['ID'] , $result_group['_kwps_hash']);

            foreach( $user_hashes as $user_hash ) {
                $result_profile = Result_Profile::get_result_profile_by_version_and_hash($version['ID'] , $user_hash);

                for($index = 0; $index < sizeof( $grouped_result_profiles ); $index++ ) {
                    if( isset( $result_profile['ID'] ) ) {
                        if( in_array($result_profile['ID'], $grouped_result_profiles[$index]) ) {
                            $version_totals[$index] = $version_totals[$index] + 1;
                        }
                    } else {
                        if( in_array(-1, $grouped_result_profiles[$index]) ) {
                            $version_totals[$index] = $version_totals[$index] + 1;
                        }
                    }

                }
            }
            $total_result_profiles_of_version = array_sum($version_totals);

            foreach( $version_totals as $key => $value ) {
                if( $value > 0 ) {
                    $version_totals[$key] = $total_result_profiles_of_version * 100 / $value;
                }
            }

            $versions_data[] = array( 'name' => $version['post_title'] , 'data' => $version_totals );

        }

        $data = array( $test_collection['post_title'], $titles, $versions_data);

        $bar_chart = static::get_chart($data);

        return $bar_chart;
    }

    public static function get_chart_per_question_per_test_collection( $test_collection_id, $result_hash ) {
        $result_group = Result_Group::get_by_result_hash( $result_hash );
        // var_dump( 'result group: ', $result_group );
        if( ! $result_group) {
            return 0;
        }

        $test_collection = Test_Collection::get_as_array($test_collection_id);
        $first_version = Version::get_one_by_post_parent($test_collection_id);

        $versions = Version::get_all_by_post_parent($test_collection_id);

        // var_dump( $versions );

        $result_profiles_of_first_version = Result_Profile::get_all_by_post_parent($first_version['ID']);

        $titles = array();
        $grouped_result_profiles = array();

        foreach( $result_profiles_of_first_version as $result_profile ) {
            $title = $result_profile['post_title'] . '<br>';
            $result_profile_ids = array( $result_profile['ID'] );

            foreach( $versions as $version ) {
                if( $first_version['ID'] != $version['ID'] ) {
                    $matching_result_profile = Result_Profile::get_result_profile_by_version_and_min_max_value(
                        $version['ID'] , $result_profile['_kwps_min_value'], $result_profile['_kwps_max_value']
                    );

                    $title .= $matching_result_profile['post_title'] . '<br>';
                    $result_profile_ids[] = $matching_result_profile['ID'];
                }
            }

            $titles[] = $title;
            $grouped_result_profiles[] = $result_profile_ids;
        }

        $titles[] = 'No result profile';
        $grouped_result_profiles[] = array(-1);

        $versions_data = array();

        foreach( $versions as $version ) {

            $version_totals = array_fill(0, sizeof(  $grouped_result_profiles ), 0 );
            $user_hashes = Entry::get_all_user_hashes_per_version( $version['ID'] , $result_group['_kwps_hash']);
            // var_dump( 'user_hashes for result group hash: ' .  $result_group['_kwps_hash'], $user_hashes );

            foreach( $user_hashes as $user_hash ) {
                $result_profile = Result_Profile::get_result_profile_by_version_and_hash($version['ID'] , $user_hash);

                for($index = 0; $index < sizeof( $grouped_result_profiles ); $index++ ) {
                    if( isset( $result_profile['ID'] ) ) {
                        if( in_array($result_profile['ID'], $grouped_result_profiles[$index]) ) {
                            $version_totals[$index] = $version_totals[$index] + 1;
                        }
                    } else {
                        if( in_array(-1, $grouped_result_profiles[$index]) ) {
                            $version_totals[$index] = $version_totals[$index] + 1;
                        }
                    }

                }
            }
            $total_result_profiles_of_version = array_sum($version_totals);

            foreach( $version_totals as $key => $value ) {
                if( $value > 0 ) {
                    $version_totals[$key] = $total_result_profiles_of_version * 100 / $value;
                }
            }

            $versions_data[] = array( 'name' => $version['post_title'] , 'data' => $version_totals );

        }

        // var_dump( $versions_data ); die;

        $data = array( $test_collection['post_title'], $titles, $versions_data);

        $bar_chart = static::get_chart($data);

        return $bar_chart;
    }

    public static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);

        return $request_data;
    }

    public static function get_chart($data){
        $title = $data[0];
        $categories = $data[1];
        $version_data = $data[2];

        return array(
            'chart' => array( 'type' => 'column' ),
            'title' => array( 'text' => $title ),
            'xAxis' => array(
                                'categories' => $categories,
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
            'series' => $version_data,
        );
    }
} 