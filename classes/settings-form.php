<?php
/**
 * Created by PhpStorm.
 * User: koengabriels
 * Date: 26/02/15
 * Time: 16:30
 */

namespace kwps_classes;


class Settings_Form {

    protected $data;

    protected $meta_errors;
    protected $coll_outro_errors;
    protected $validation_result;
    protected $is_valid;

    public function __construct( $data ) {
        $this->data = $data;
        $this->meta_errors = array();
        $this->coll_outro_errors = array();
        $this->validate_data();
    }

    public function save() {
        if( isset( $this->data['_kwps_show_grouping_form'] ) ) {
            if( sizeof( $this->coll_outro_errors ) > 0 || sizeof( $this->meta_errors ) > 0 ) {
                $data = $this->data;
                $data['errors'] = $this->meta_errors;
                $data['collection_outro']['errors'] = $this->coll_outro_errors;
                return $data;
            } else {
                $this->save_meta();
                return $this->save_collection_outro();
            }
        } else {
            if( sizeof( $this->meta_errors ) > 0 ) {
                $data = $this->data;
                $data['errors'] = $this->meta_errors;
                return $data;
            } else {
                $this->save_meta();
                return $this->data;
            }
        }
    }

    private function save_meta() {
        $meta_data = array_diff_key($this->data, array( 'collection_outro' => '', 'ID' => '' ) );
        foreach( $meta_data as $meta_key => $meta_value ) {
            update_post_meta( $this->data['ID'], $meta_key, $meta_value );
        }
        if( ! isset( $meta_data['_kwps_show_grouping_form'] ) ) {
            delete_post_meta( $this->data['ID'], '_kwps_show_grouping_form' );
        }
    }

    public function validate_data() {
        if(! isset( $this->data['ID'] ) ) {
            $this->meta_errors['ID'] = __('Required' );
        }
        $this->validate_test_collection_meta();

        if( isset( $this->data['_kwps_show_grouping_form'] ) ) {
            $this->validate_coll_outro();
        }
    }

    private function validate_test_collection_meta() {
        $allowed_dropdown_values = \kwps_classes\Test_Collection::$allowed_dropdown_values;

        foreach( Test_Collection::$meta_data_fields as $meta_field ) {
            if( in_array( $meta_field, Test_Collection::$required_fields ) &&
                ( ! isset( $this->data[$meta_field] ) || empty( $this->data[$meta_field] ) ) ){
                $this->meta_errors[$meta_field] = __( 'Required' );
            } else {
                if( in_array( $meta_field, array_keys( $allowed_dropdown_values ) ) ) {
                    if( ! in_array( $this->data[$meta_field], $allowed_dropdown_values[$meta_field] ) ) {
                        $this->meta_errors[$meta_field] = __( 'Invalid limit used' );
                    }
                }
            }
        }
    }

    private function validate_coll_outro() {
        $this->coll_outro_errors = Coll_Outro::validate_for_insert( $this->data['collection_outro'], true ) ;

        if( sizeof( $this->coll_outro_errors ) == 0  ) {
            $test_modus = Test_Collection::get_test_modus( $this->data['collection_outro']['post_parent'] );

            if( ! Coll_Outro::has_valid_result_code_in_post_content( $this->data['collection_outro'], $test_modus ) ) {
                $this->coll_outro_errors['post_content'] = __( 'No valid result shortcode used' );
            }
        }
    }

    private function save_collection_outro() {
        $coll_outro_id = Coll_Outro::save_post( $this->data['collection_outro'], true );
        $data = $this->data;
        $data['collection_outro']['ID'] = $coll_outro_id;
        return $data;
    }
} 