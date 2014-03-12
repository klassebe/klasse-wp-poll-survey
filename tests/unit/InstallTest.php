<?php

class InstallTest extends Base_UnitTestCase {

    private $kwps;
    public $pluginSlug = 'klasse-wp-poll-survey';

    function setUp() {

        parent::setUp();
        $this->kwps = new Klasse_WP_Poll_Survey();

    } // end setup


    function testPluginInitialization() {
        $this->assertFalse( null == $this->kwps );
    } // end testPluginInitialization

    function testCrazyThing() {
        $pluginTablePrefix = $this->wpdb->prefix . 'kwps_';

        Klasse_WP_Poll_Survey::activate();

        $tables = $this->wpdb->get_results('show tables like "' . $pluginTablePrefix . '%";');

        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        $this->assertTrue(count($tables) > 0);
    }
}