<?php

namespace includes;

    class Duplicate {

        public static function register_post_status(){
            register_post_status( 'duplicate', array(
                'label'                     => _x( 'Duplicate', 'kwps_test_modus' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Duplicate <span class="count">(%s)</span>', 'Duplicate <span class="count">(%s)</span>' ),
            ) );
        }

        public static function display_post_status($states){
            global $post;

            if( 'kwps_test_modus' == $post->post_type && 'duplicate' == $post->post_status ){
                $states[] = __('Duplicate');
            }
            return $states;
        }
    }