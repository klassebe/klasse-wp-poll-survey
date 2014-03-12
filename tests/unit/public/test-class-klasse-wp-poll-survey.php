<?php

class ClassKlasseWpPollSurveyTest extends WP_UnitTestCase {

    protected $wpdb;
    private $kwps;
    private $table_prefix = 'kwps_';
    public $pluginSlug = 'klasse-wp-poll-survey';

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    function setUp() {

        parent::setUp();

        remove_filter( 'query', array( $this, '_create_temporary_tables' ) );
        remove_filter( 'query', array( $this, '_drop_temporary_tables' ) );

        $this->kwps = new Klasse_WP_Poll_Survey();


    } // end setup

    function testGetPluginSlug()
    {
        $this->assertTrue('klasse-wp-poll-survey' == $this->kwps->get_plugin_slug());
    }

    function testGetInstance()
    {
        $this->assertTrue(Klasse_WP_Poll_Survey::get_instance() instanceof Klasse_WP_Poll_Survey);
    }

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

    function testLoadPluginTextDomain()
    {
        $this->assertNull($this->kwps->load_plugin_textdomain());
    }

    function testEnqueueStyles()
    {
        $this->assertNull($this->kwps->enqueue_styles());
    }

    function testEnqueueScripts()
    {
        $this->assertNull($this->kwps->enqueue_scripts());
    }

    function testActivateNewSite()
    {
        $this->assertNull($this->kwps->activate_new_site( rand()));
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