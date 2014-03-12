<?php

class Base_UnitTestCase extends WP_UnitTestCase {
    protected $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    function setUp() {

        parent::setUp();

        remove_filter( 'query', array( $this, '_create_temporary_tables' ) );
        remove_filter( 'query', array( $this, '_drop_temporary_tables' ) );

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