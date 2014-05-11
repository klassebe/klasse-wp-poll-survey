<?php
namespace includes;

class Uniqueness {
    public static function is_allowed() {

    }

    public static function is_logged_in() {

    }

    public static function get_types() {
        return array(
            array(
                'function' => 'free',
                'label' => __('Free')
            ),
            array(
                'function' => 'ip',
                'label' => __('Limit on IP-address')
            ),
            array(
                'function' => 'cookie',
                'label' => __('Limit on cookie')
            )
        );
    }


}