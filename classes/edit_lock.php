<?php

namespace kwps_classes;


class Edit_Lock {

    public static function unlock_test() {
        $request_data = static::get_post_data_from_request();

        if( isset( $request_data['ID'] ) ) {
            $requested_post = get_post( $request_data['ID']);

            if( $requested_post ) {
                if( get_post_type( $request_data['ID'] ) == Version::$post_type ) {
                    $version = Version::get_as_array( $request_data['ID'] );
                    $test_collection_id = $version['post_parent'];
                } elseif( get_post_type( $request_data['ID'] ) == Test_Collection::$post_type ) {
                    $test_collection_id = $request_data['ID'];
                }


                $in_use_by = get_post_meta( $test_collection_id, '_kwps_in_use_by', true );
                if( empty( $in_use_by ) ) {
                    wp_send_json_error(
                        array(
                            'message' => 'Trying to unlock an unlocked test... Really??'
                        )
                    );
                } else {
                    if( $in_use_by == get_current_user()  ) {
                        delete_post_meta( $test_collection_id, '_kwps_in_use_by' );
                        wp_send_json_success( array( 'message' => 'Test collection with id ' . $test_collection_id . ' unlocked.') );
                    } else {
                        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
                        wp_send_json_error( array( 'message' => 'nice try!') );
                    }
                }
                die;
            }
        }

        header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
        wp_send_json_error( array( 'message' => 'invalid id') );
        die;
    }

    /**
     * Returns the decoded json data from a http request
     *
     * @return array|mixed
     */
    private static function get_post_data_from_request(){
        $json = file_get_contents("php://input");
        $request_data = json_decode($json, true);

        return $request_data;
    }
} 