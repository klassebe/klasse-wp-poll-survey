<?php

class VersionHandlerTest extends WP_UnitTestCase {

    function test_new_poll_version_form_validation_Empty() {
        $this->checkOutputWithFormTestData( 'poll/empty.php');
    }

    function test_new_poll_version_form_validation_NoIntroIndex() {
        $this->checkOutputWithFormTestData( 'poll/no-intro-index.php');
    }

    function test_new_poll_version_form_validation_NoOutroIndex() {
        $this->checkOutputWithFormTestData( 'poll/no-outro-index.php');
    }

    function test_new_poll_version_form_validation_NoQuestionGroupsIndex() {
        $this->checkOutputWithFormTestData( 'poll/no-question-groups-index.php');
    }

    function test_new_poll_version_form_validation_NoIntroResultIndex() {
        $this->checkOutputWithFormTestData( 'poll/no-intro-result-index.php');
    }

    function test_new_poll_version_form_validation_NotAnArray() {
        $this->checkOutputWithFormTestData( 'poll/not-an-array.php');
    }

    function checkOutputWithFormTestData($file) {
        $test_data = include __DIR__ . '/../form-test-data/new-version/' . $file;
        $version_handler = new \kwps_classes\Version_Handler();
        $output = $version_handler->validate_new_version_form( $test_data['input'] );
//        var_dump( $output['data'], $test_data['expected_output']['data'] );
        $this->assertEquals($output['errors'], $test_data['expected_output']['errors']);
        $this->assertTrue( $this->arrays_are_similar( $output['data'], $test_data['expected_output']['data'] ) );
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
        // if the indexes don't match, return immediately
        $array_keys_a = array_keys( $a );
        $array_keys_b = array_keys( $b );

        if( asort( $array_keys_a ) !== asort( $array_keys_b ) ) {
            return false;
        }

        // we know that the indexes, but maybe not values, match.
        // compare the values between the two arrays
        foreach($a as $k => $v) {
            if( is_array( $v ) ) {
                if(! $this->arrays_are_similar( $v, $b[$k] ) ) {
//                    var_dump($k, $v, $b[$k]);
                    return false;
                }
            } else {
                if ($v !== $b[$k]) {
//                    var_dump($k, $v, $b[$k]);
                    return false;
                }
            }
        }
        // we have identical indexes, and no unequal values
        return true;
    }
}
