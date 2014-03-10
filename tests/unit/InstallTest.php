<?php

class InstallTest extends Base_UnitTestCase {

    private $kwps;
    private $table_prefix = 'kwps_';
    public $pluginSlug = 'klasse-wp-poll-survey';

    function setUp() {

        parent::setUp();

        remove_filter( 'query', array( $this, '_create_temporary_tables' ) );
        remove_filter( 'query', array( $this, '_drop_temporary_tables' ) );

        $this->kwps = new Klasse_WP_Poll_Survey();

    } // end setup

    function testPluginInitialization() {
        $this->assertFalse( null == $this->kwps );
    } // end testPluginInitialization


    function testPluginActivation() {
        $pluginTablePrefix = $this->wpdb->prefix . $this->table_prefix;

        Klasse_WP_Poll_Survey::activate();

        $this->assertTrue(count($this->wpdb->get_results("SHOW TABLES LIKE '$pluginTablePrefix%'")) > 0);
    }

    /**
     * @depends testPluginInitialization
     */
    function testPluginDeactivation() {
        $pluginTablePrefix = $this->wpdb->prefix . $this->table_prefix;

        Klasse_WP_Poll_Survey::deactivate();

        $this->assertTrue(count($this->wpdb->get_results("SHOW TABLES LIKE '$pluginTablePrefix%'")) == 0);
    }
}