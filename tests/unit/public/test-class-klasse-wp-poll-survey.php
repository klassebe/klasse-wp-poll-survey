<?php
use RedBean_Facade as R;

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
    }

    /**
     * @depends testPluginActivation
     */
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
        R::wipe( 'mode' );
        $this->kwps->addTestModi();

        $installedModi = R::findAll( 'mode' );
        $testModi = $this->kwps->getAvailableTestModi();

        $this->assertTrue(count($testModi) == count($installedModi));
    }

    /**
     * @depends testPluginInitialization
     */
    function testPluginUninstall() {
        Klasse_WP_Poll_Survey::uninstall();
    }
}