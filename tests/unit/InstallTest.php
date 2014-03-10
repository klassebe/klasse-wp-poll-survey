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

    function testPluginActivation() {
        $pluginTablePrefix = $this->wpdb->prefix . 'kwps_status';

        Klasse_WP_Poll_Survey::activate();

        // Stop here and mark this test as incomplete.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        $this->assertTrue($this->wpdb->get_var("SHOW TABLES LIKE '$pluginTablePrefix'") == $pluginTablePrefix);
    }
}