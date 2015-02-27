<?php

require_once __DIR__ . '/../classes/settings-form.php';

class Test_Settings_Form extends WP_UnitTestCase {

    protected $test_modus_name;
    protected $test_data_folder;

    function setUp(  ) {
        parent::setUp();

        ini_set('xdebug.var_display_max_depth', 25);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 2048);

        $this->truncate_tables();

        \kwps_classes\Test_Modus::create_default_test_modi();
        $test_modi = get_posts( array(
                'post_type' => 'kwps_test_modus',
                'name' => 'kwps-personality-test',
                'post_status' => 'publish',
            )
        );

        $test_modus_id = $test_modi[0]->ID;

        $this->test_collection = \kwps_classes\Test_Collection::save_post( array(
            'post_title' => 'Test collection',
            'post_parent' => $test_modus_id,
            '_kwps_logged_in_user_limit' => 'free',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
        ) );
    }

    function test_save_valid_meta_valid_coll_outro() {
        $settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'free',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'collection_outro' =>   array(
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $expected_settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'free',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'collection_outro' =>   array(
                'ID' => 5,
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $this->check_form_output( $settings, $expected_settings );
    }

    function test_save_invalid_meta_valid_coll_outro(){
        $settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => '',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'collection_outro' =>   array(
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $expected_settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => '',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'errors' => array(
                '_kwps_logged_in_user_limit' => __( 'Required' ),
            ),
            'collection_outro' =>   array(
                'errors' => array(),
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $this->check_form_output( $settings, $expected_settings );
    }

    function test_save_valid_meta_invalid_coll_outro() {
        $settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'free',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'collection_outro' =>   array(
                'post_parent' => '',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $expected_settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'free',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'errors' => array(),
            'collection_outro' =>   array(
                'errors' => array(
                    'post_parent' => __( 'Required' ),
                ),
                'post_parent' => '',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $this->check_form_output( $settings, $expected_settings );
    }

    function test_save_valid_meta_no_result_shortcode() {
        $settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'free',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'collection_outro' =>   array(
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => 'blabla',
            ),
        );

        $expected_settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'free',
            '_kwps_logged_out_user_limit' => 'free',
            '_kwps_show_grouping_form' => '1',
            'errors' => array(),
            'collection_outro' =>   array(
                'errors' => array(
                    'post_content' => __( 'No valid result shortcode used' ),
                ),
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => 'blabla',
            ),
        );

        $this->check_form_output( $settings, $expected_settings );
    }

    function test_save_with_grouping_form_changed_limits(){
        $settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'cookie',
            '_kwps_logged_out_user_limit' => 'none',
            '_kwps_show_grouping_form' => '1',
            'collection_outro' =>   array(
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $expected_settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'cookie',
            '_kwps_logged_out_user_limit' => 'none',
            '_kwps_show_grouping_form' => '1',
            'collection_outro' =>   array(
                'ID' => 5,
                'post_parent' => '4',
                'post_status' => 'draft',
                'post_content' => '[kwps_result result=grouped-bar-chart-per-profile]',
            ),
        );

        $this->check_form_output( $settings, $expected_settings );

        $this->assertEquals( get_post_meta( 4, '_kwps_logged_in_user_limit', true), 'cookie' );
        $this->assertEquals( get_post_meta( 4, '_kwps_logged_out_user_limit', true), 'none' );
    }

    function test_save_no_grouping_form_valid() {
        $settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'cookie',
            '_kwps_logged_out_user_limit' => 'none',
        );

        $expected_settings = array (
            'ID' => "4",
            '_kwps_logged_in_user_limit' => 'cookie',
            '_kwps_logged_out_user_limit' => 'none',
        );

        $this->check_form_output( $settings, $expected_settings );

        $this->assertEquals( get_post_meta( 4, '_kwps_logged_in_user_limit', true), 'cookie' );
        $this->assertEquals( get_post_meta( 4, '_kwps_logged_out_user_limit', true), 'none' );
    }

    function check_form_output($settings, $expected_settings ){
        $settings_form = new \kwps_classes\Settings_Form( $settings );
        $this->assertTrue( $this->arrays_are_similar( $settings_form->save(), $expected_settings  ) );
    }

    /**
     * Determine if two associative arrays are similar
     *
     * Both arrays must have the same indexes with identical values
     * without respect to key ordering
     *
     * @param array $a
     * @param array $b
     * @return bool
     */
    function arrays_are_similar($a, $b) {
        if(! is_array( $a) ) {
            return false;
        }

        if(! is_array( $b) ) {
            return false;
        }

        $sorted_a = $this->sort_array_by_key( $a );
        $sorted_b = $this->sort_array_by_key( $b );

        if ( $sorted_a === $sorted_b ) {
            return true;
        } else {
            var_dump( $sorted_a, $sorted_b);
            return false;
        }
    }

    function sort_array_by_key( $a ) {
        ksort( $a );

        foreach( $a as $key => $value ) {
            if( is_array( $value ) ) {
                $a[$key] = $this->sort_array_by_key( $value );
            }
        }

        return $a;
    }

    function tearDown()
    {
        parent::tearDown();
        $this->truncate_tables();
    }

    function truncate_tables() {
        global $wpdb;

        $wpdb->query( 'TRUNCATE ' . $wpdb->posts );
        $wpdb->query( 'TRUNCATE ' . $wpdb->postmeta );
    }
} 