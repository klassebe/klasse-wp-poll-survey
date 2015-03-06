<?php

namespace kwps_classes;


class Edit_Lock {

    public static function unlock_by_test_collection_id() {
        $request_data = static::get_post_data_from_request();

        if( isset( $request_data['ID'] ) ) {
            $test_collection = Test_Collection::get_as_array( $request_data['ID'], true);

            if( $test_collection ) {
                $in_use_by = get_post_meta( $request_data['ID'], '_kwps_in_use_by', true );
                if( empty( $in_use_by ) ) {
                    wp_send_json_error(
                        array(
                            'message' => 'Trying to unlock an unlocked test... Really??'
                        )
                    );

                } else {
                    if( $in_use_by == get_current_user()  ) {
                        delete_post_meta( $request_data['ID'], '_kwps_in_use_by' );
                        wp_send_json_success( array( 'message' => 'Test collection with id ' .$request_data['ID'] . ' unlocked.') );
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

    public static function unlock_by_version_id() {
        $request_data = static::get_post_data_from_request();

        if( isset( $request_data['ID'] ) ) {
            $version = Version::get_as_array( $request_data['ID'], true);

            if( $version ) {
                $in_use_by = get_post_meta( $version['post_parent'], '_kwps_in_use_by', true );
                if( empty( $in_use_by ) ) {
                    wp_send_json_error(
                        array(
                            'message' => 'Trying to unlock an unlocked test... Really??'
                        )
                    );

                } else {
                    if( $in_use_by == get_current_user()  ) {
                        delete_post_meta( $version['post_parent'], '_kwps_in_use_by' );
                        wp_send_json_success( array( 'message' => 'Test collection with id ' .$request_data['ID'] . ' unlocked.') );
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