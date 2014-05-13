<?php

namespace includes;

    abstract class Kwps_Post_Status {

        public static $post_status = '';

        public static $label_singular ='';
        public static $label_plural ='';

        public static $available_post_types = array();


        public static function register_post_status(){
            $args = array(
                'label'                     => _x( static::$label_singular, 'kwps_post_status' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( static::$label_singular . ' <span class="count">(%s)</span>', static::$label_plural . ' <span class="count">(%s)</span>' ),
            );

            register_post_status( static::$post_status, $args );
        }

        public static function display_post_status($states){
            global $post;

            if( in_array($post->post_type, static::$available_post_types) ) {
                if(static::$post_status == $post->post_status) {
                    $states[] = __(static::$label_singular);
                }
            }

//            if( 'kwps_test_modus' == $post->post_type && 'duplicate' == $post->post_status ){
//                $states[] = __('Duplicate');
//            }
            return $states;
        }
    }