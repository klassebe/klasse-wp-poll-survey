<?php
namespace includes;

class Uniqueness {

    public static function set_cookie(){
        $bits = 50;
        $uid = bin2hex(openssl_random_pseudo_bytes($bits));

        if (!isset($_COOKIE['klasse_wp_poll_survey'])) {
            setcookie('klasse_wp_poll_survey', $uid, time() + (10 * 365 * 24 * 60 * 60));
        }
    }

    public static function is_allowed() {

    }

    public static function is_logged_in() {

    }

    public static function get_types() {
        return array(
            array(
                'function' => 'free',
                'label' => __('Free'),
                'description' => __('Completely free to anyone'),
            ),
            array(
                'function' => 'cookie',
                'label' => __('Limit on cookie'),
                'description' => __('Based on session cookie'),

            ),
            array(
                'function' => 'ip',
                'label' => __('Limit on IP-address'),
                'description' => __('Based on ip address'),
            ),
            array(
                'function' => 'logged_in',
                'label' => __('Must be logged in'),
                'description' => __('Only logged in users'),
            ),
//            array(
//                'function' => 'mixed',
//                'label' => __('Mixed mode'),
//                'description' => __('Based on ip address'),
//            ),

        );
    }

    public static function free($test_collection_id){
        return true;
    }

    public static function cookie($test_collection_id){
        $versions = Version::get_all_children($test_collection_id);

        $entries = array();

        foreach($versions as $version){
            $entries_for_version = Entry::get_all_children($version['ID']);
            array_push($entries, $entries_for_version);
        }

        if( ! isset($_COOKIE['klasse_wp_poll_survey']) ){
            return true;
        } else {
            foreach($entries as $entry){
                if( $entry['_kwps_cookie_value'] == $_COOKIE['klasse_wp_poll_survey']){
                    return true;
                    break;
                }
            }

            return false;
        }
    }


}