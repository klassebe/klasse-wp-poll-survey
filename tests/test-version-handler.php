<?php

class VersionHandlerTest extends WP_UnitTestCase {

    protected $test_modus_poll;
    protected $test_collection;

    function setUp()
    {
        parent::setUp();
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
    }

    function test_new_poll_version_form_validation_Empty() {
        $this->checkOutputWithFormTestData( 'poll/empty.php', false);
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
        $this->checkOutputWithFormTestData( 'poll/not-an-array.php', false);
    }

    function test_new_poll_version_form_validation_tooManyQuestionGroups() {
        $this->checkOutputWithFormTestData( 'poll/too-many-question-groups.php');
    }

    function test_new_poll_version_form_validation_tooManyQuestions() {
        $this->checkOutputWithFormTestData( 'poll/too-many-questions.php');
    }

    function test_new_poll_version_form_validation_valid() {
        $this->checkOutputWithFormTestData( 'poll/valid.php');
    }

    function checkOutputWithFormTestData($file, $link_to_test_collection = true) {
        $test_data = include __DIR__ . '/../form-test-data/new-version/' . $file;
        $input = $test_data['input'];
        $expected_output = $test_data['expected_output'];

        if( $link_to_test_collection ) {
            $input['post_parent'] = $this->test_collection['ID'];
            $expected_output['data']['post_parent'] = $this->test_collection['ID'];
        }

        $version_handler = new \kwps_classes\Version_Handler();
        $output = $version_handler->validate_new_version_form( $input );

//        var_dump( $expected_output['test_modus_errors'], $output['test_modus_errors']);

        $this->assertEquals($output['errors'], $expected_output['errors']);
        $this->assertEquals($output['test_modus_errors'], $expected_output['test_modus_errors']);
        $this->assertTrue( $this->arrays_are_similar( $output['data'], $expected_output['data'] ) );
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
        if( is_array( $a) != is_array( $b) ) {
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

        // if the indexes don't match, return immediately
//        $array_keys_a = array_keys( $a );
//        $array_keys_b = array_keys( $b );
//
//        $array_keys_diff_1 = array_diff( $array_keys_a, $array_keys_b );
//        $array_keys_diff_2 = array_diff( $array_keys_b, $array_keys_a );
//        $unique_keys = array_merge($array_keys_diff_1, $array_keys_diff_2);
//
//        if( sizeof( $unique_keys ) > 0 ) {
//            return false;
//        }
//
//        // we know that the indexes, but maybe not values, match.
//        // compare the values between the two arrays
//        foreach($a as $k => $v) {
//            if( is_array( $v ) ) {
//                if(! $this->arrays_are_similar( $v, $b[$k] ) ) {
//                    var_dump($k, $v, $b[$k]);
//                    return false;
//                }
//            } else {
//                if ($v !== $b[$k]) {
//                    var_dump($k, $v, $b[$k]);
//                    return false;
//                }
//            }
//        }
//        // we have identical indexes, and no unequal values
//        return true;
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
