<?php

abstract class Kwps_Test extends WP_UnitTestCase {
    protected $test_modus_name;
    protected $test_data_folder;
    protected $existing_versions = array();

    function setUp(  ) {
        parent::setUp();

        ini_set('xdebug.var_display_max_depth', 25);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 2048);

        $this->truncate_tables();

        \kwps_classes\Test_Modus::create_default_test_modi();
        $test_modi = get_posts( array(
                'post_type' => 'kwps_test_modus',
                'name' => $this->test_modus_name,
                'post_status' => 'publish',
            )
        );

        $test_modus_id = $test_modi[0]->ID;

        $this->test_collection = \kwps_classes\Test_Collection::save_post( array(
            'post_title' => 'Test collection',
            'post_parent' => $test_modus_id,
        ) );

        $version_handler = new \kwps_classes\Version_Handler();

        $test_data = include $this->test_data_folder . 'fixture.php';

        foreach( $test_data as $new_version ) {
            $returned_version = $version_handler->save_new_version_form( $new_version );
            $this->existing_versions[] = $returned_version;
        }

    }

    function checkOutPutWithFormTestData( $file ){
        $test_data = include $this->test_data_folder . $file;
        $input = $test_data['input'];
        $expected_output = $test_data['expected_output'];

        $version_handler = new \kwps_classes\Version_Handler();
        $output = $version_handler->validate_existing_version_form( $input );

        $this->assertEquals($output['errors'], $expected_output['errors']);
        $this->assertEquals($output['test_modus_errors'], $expected_output['test_modus_errors']);
        $this->assertTrue( $this->arrays_are_similar( $output['data'], $expected_output['data'] ) );
    }

    function check_saved_and_updated_siblings( $file ) {
        $test_data = include $this->test_data_folder . $file;
        $input = $test_data['input'];
        $expected_output_version_1 = $test_data['expected_output'][0];
        $expected_output_version_2 = $test_data['expected_output'][1];
        $expected_output_version_3 = $test_data['expected_output'][2];

        $version_handler = new \kwps_classes\Version_Handler();
        $output = $version_handler->save_existing_version_form( $input );

        $this->assertTrue( $this->arrays_are_similar( $output, $expected_output_version_1['data'] ) );

        $from_db = \kwps_classes\Version::get_with_all_children( $output['ID'] );
        $this->assertTrue( $this->arrays_are_similar( $expected_output_version_1['data'], $from_db ) );

        // Test if the 2 other versions have answer_option removed as well
        $from_db_version_2 = \kwps_classes\Version::get_with_all_children( $this->existing_versions[1]['ID'] );
        $this->assertTrue( $this->arrays_are_similar( $expected_output_version_2['data'], $from_db_version_2 ) );

        $from_db_version_3 = \kwps_classes\Version::get_with_all_children( $this->existing_versions[2]['ID'] );
        $this->assertTrue( $this->arrays_are_similar( $expected_output_version_3['data'], $from_db_version_3 ) );
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