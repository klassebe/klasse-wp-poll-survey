<?php

class Base_UnitTestCase extends WP_UnitTestCase {
    protected $wpdb;
    protected $kwps;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    function setUp() {

        parent::setUp();
        $this->kwps = new Klasse_WP_Poll_Survey();

    } // end setup

    static function setUpBeforeClass() {
        parent::setUpBeforeClass();
        Klasse_WP_Poll_Survey::activate();
    }

    static function tearDownAfterClass() {
        parent::setUpBeforeClass();
        Klasse_WP_Poll_Survey::uninstall();
    }


}