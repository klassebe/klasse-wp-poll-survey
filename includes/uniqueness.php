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

    public static function is_allowed($answer_option_id, $function_name = 'none') {
        $is_allowed = false;

        switch($function_name){
            case 'free' : $is_allowed = static::is_always_allowed();
                break;
            case 'cookie' : $is_allowed = static::is_allowed_by_cookie($answer_option_id);
                break;
            case 'ip' : $is_allowed = static::is_allowed_by_ip($answer_option_id);
                break;
            case 'once' : $is_allowed = static::is_allowed_by_user_id($answer_option_id);
                break;
            case 'none' : $is_allowed = static::is_never_allowed();
                break;
        }

        return $is_allowed;
    }

    public static function is_logged_in() {

    }

    public static function get_options_for_logged_in_users(){
        return array(
            array(
                'label' => __('Free'),
                'function' => 'free'
            ),
            array(
                'label' => __('Once, based on cookie'),
                'function' => 'cookie'
            ),
            array(
                'label' => __('Once, based on IP'),
                'function' => 'ip'
            ),
            array(
                'label' => __('Once, based login'),
                'function' => 'once'
            )
        );
    }

    public static function get_options_for_logged_out_users(){
        return array(
            array(
                'label' => __('Free'),
                'function' => 'free'
            ),
            array(
                'label' => __('Once, based on cookie'),
                'function' => 'cookie'
            ),
            array(
                'label' => __('Once, based on IP'),
                'function' => 'ip'
            ),
            array(
                'label' => __('Only logged in users allowed'),
                'function' => 'none'
            )
        );
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

    public static function is_always_allowed(){
        return true;
    }

    public static function is_allowed_by_cookie($answer_option_id){
        $entries = Entry::get_all_children($answer_option_id);

        if( ! isset($_COOKIE['klasse_wp_poll_survey']) ){
            return false;
        } else {
            foreach($entries as $entry){
                if( $entry['_kwps_cookie_value'] == $_COOKIE['klasse_wp_poll_survey']){
                    return false;
                }
            }
            return true;
        }
    }

    public static function is_allowed_by_ip($answer_option_id){
        $entries = Entry::get_all_children($answer_option_id);
        $ip_of_current_user = static::get_ip_of_user();

        foreach( $entries as $entry){
            if( $ip_of_current_user ==  $entry['_kwps_ip_address'] ) {
                return false;
            }
        }
        return true;
    }

    public static function is_allowed_by_user_id($answer_option_id){
        $entries = Entry::get_all_children($answer_option_id);
        $current_user_id = get_current_user_id();

        foreach( $entries as $entry){
            if( $current_user_id ==  $entry['post_author'] ) {
                return false;
            }
        }
        return true;
    }

    public static function get_ip_of_user(){
        if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    public static function is_never_allowed(){
        return false;
    }


}