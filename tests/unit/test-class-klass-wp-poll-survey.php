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

        foreach(Klasse_WP_Poll_Survey::$tables as $table) {
            $tableName = $pluginTablePrefix . $table;
            $result = $this->wpdb->get_var("SHOW TABLES LIKE '$tableName'");
            if(!$result) {
                echo 'Failed Table: ' . $result;
            }

            $this->assertTrue($result == $tableName);
        }
    }

    function testGetDefaultAvailableModi()
    {
        $testModi = $this->kwps->getAvailableTestModi();
        $this->assertTrue(count($testModi) == 2);
    }

    function testGetInstalledDefaultAvailableModi()
    {
        $this->kwps->addTestModi();
        $pluginTablePrefix = $this->wpdb->prefix . $this->table_prefix;

        $installedModi = $this->wpdb->get_results("SELECT * FROM {$pluginTablePrefix}mode");
        $testModi = $this->kwps->getAvailableTestModi();

        $this->assertTrue(count($testModi) == count($installedModi));
    }

    /**
     * @depends testPluginInitialization
     */
    function testPluginUninstall() {
        $pluginTablePrefix = $this->wpdb->prefix . $this->table_prefix;

        Klasse_WP_Poll_Survey::uninstall();

        $this->assertTrue(count($this->wpdb->get_results("SHOW TABLES LIKE '$pluginTablePrefix%'")) == 0);
    }
}