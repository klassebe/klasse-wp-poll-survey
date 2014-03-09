<?php

class Base_UnitTestCase extends WP_UnitTestCase {
    protected $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
}