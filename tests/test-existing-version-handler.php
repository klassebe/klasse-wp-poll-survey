<?php

class Existing_Version_Handler_Test extends WP_UnitTestCase {
    protected $test_modus_poll;
    protected $test_collection;
    protected $existing_version;

    function setUp()
    {
        parent::setUp();

        ini_set('xdebug.var_display_max_depth', 25);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 2048);

        $this->truncate_tables();

        \kwps_classes\Test_Modus::create_default_test_modi();
        $polls = get_posts( array(
                'post_type' => 'kwps_test_modus',
                'name' => 'kwps-poll',
                'post_status' => 'publish',
            )
        );

        $poll_modus_id = $polls[0]->ID;
        $this->test_modus_poll = \kwps_classes\Test_Modus::get_as_array( $poll_modus_id );

        $this->test_collection = \kwps_classes\Test_Collection::save_post( array(
            'post_title' => 'Poll collection',
            'post_parent' => $poll_modus_id,
        ) );

        $test_data = include __DIR__ . '/../form-test-data/new-version/poll/valid-for-save-test.php';
        $input = $test_data['input'];
        $expected_output = $test_data['expected_output'];

        $version_handler = new \kwps_classes\Version_Handler();
        $this->existing_version = $version_handler->save_new_version_form( $input );

    }

    function test_validate_remove_answer_option(){
        $this->checkOutPutWithFormTestData( 'removing-answer-option-test.php' );
    }

    function test_validate_remove_too_many_answer_options() {
        $this->checkOutPutWithFormTestData( 'removing-too-many-answer-options-test.php' );
    }

    function test_validate_remove_too_many_questions() {
        $this->checkOutPutWithFormTestData( 'removing-too-many-questions-test.php' );
    }

    function checkOutPutWithFormTestData( $file ){
        $test_data = include __DIR__ . '/../form-test-data/existing-version/poll/' . $file;
        $input = $test_data['input'];
        $expected_output = $test_data['expected_output'];

        $version_handler = new \kwps_classes\Version_Handler();
        $output = $version_handler->validate_existing_version_form( $input );

        $this->assertEquals($output['errors'], $expected_output['errors']);
        $this->assertEquals($output['test_modus_errors'], $expected_output['test_modus_errors']);
        $this->assertTrue( $this->arrays_are_similar( $output['data'], $expected_output['data'] ) );
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

}

