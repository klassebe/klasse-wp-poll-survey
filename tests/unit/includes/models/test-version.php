<?php

class TestVersion extends Base_UnitTestCase {

    private static $table_prefix = 'kwps_';

    protected $classLocation;
    protected $versionModel;
    protected $testData = array(
        'validTest' => array(
            'name' => 'A demo test',
            'description' => '<h1>Description</h1><p>This is a description as HTML</p>',
            'view_count' => 0,
            'user_id' => 0,
            'mode_id' => 0,
        ),
        'validVersion' => array(
            'test_id' => 0,
            'name' => 'A demo Version',
            'api_key' => '23424errz56534egre686fgd'
        )
    );
    protected $test;

    function setUp() {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;

        parent::setUp();

        require_once plugin_dir_path(__DIR__) . '../../../includes/models/version.php';
        require_once plugin_dir_path(__DIR__) . '../../../includes/models/test.php';

        $this->kwps->addTestModi();

        $mode = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}mode");
        $user = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}users");

        $testModel = new Kwps_TestModel($this->testData['validTest']);
        $testModel->setModeId($mode->id);
        $testModel->setUserId($user->ID);
        $testModel->save();

        $this->test = $testModel;
        $this->testData['validVersion']['test_id'] = $testModel->getId();

    } // end setup

    function addTestId()
    {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $test = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}test");

        $this->testData['validVersion']['test_id'] = $test->id;

        return ;
    }

    function tearDown() {
        parent::tearDown();
    } // end tearDown

    static function setUpBeforeClass() {
        parent::setUpBeforeClass();
    }

    static function tearDownAfterClass() {
        parent::tearDownAfterClass();
    }


    function testFileExists() {
        $this->assertTrue(file_exists(plugin_dir_path(__DIR__) . '../../../includes/models/version.php'));
    }

    function testClass() {
        $versionModel = new Kwps_VersionModel();
        $this->assertTrue($versionModel instanceof Kwps_VersionModel);
    }

    function testGetTablePrefix() {
        $versionModel = new Kwps_VersionModel();
        $this->assertEquals($versionModel->getTablePrefix(), 'kwps_');
    }

    function testSetTablePrefix() {
        $versionModel = new Kwps_VersionModel();
        $prefixOriginal = $versionModel->getTablePrefix();

        $prefix = 'test';
        $versionModel->setTablePrefix($prefix);
        $this->assertEquals($versionModel->getTablePrefix(), $prefix);

        $versionModel->setTablePrefix($prefixOriginal);
    }

    function testCreateOnConstruct()
    {
        $data = $this->testData['validVersion'];
        $versionModel = new Kwps_VersionModel($data);

        $this->assertTrue($versionModel->getName() == $data['name']);
        $this->assertTrue($versionModel->getApiKey() == $data['api_key']);
        $this->assertTrue($versionModel->getStatus() == 'ACT');
    }

    function testGetSingleVersion()
    {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $this->addTestId();
        $this->wpdb->insert(
            $tableDefaultPrefix . 'version',
            $this->testData['validVersion']
        );

        $versionReference = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}version");

        $versionModel = new Kwps_VersionModel();
        $versionModel->setId($versionReference->id);
        $versionModel->get();

        $this->assertNotNull($versionModel->getId());
        $this->assertEquals($versionModel->getId(), $versionReference->id);
    }

    function testGetTestId()
    {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $this->addTestId();
        $this->wpdb->insert(
            $tableDefaultPrefix . 'version',
            $this->testData['validVersion']
        );

        $versionReference = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}version");

        $versionModel = new Kwps_VersionModel();
        $versionModel->setId($versionReference->id);
        $versionModel->get();

        $this->assertNotNull($versionModel->getId());
        $this->assertEquals($versionModel->getTestId(), $versionReference->test_id);
    }

    function testUpdateVersion()
    {
        $this->addTestId();

        $versionModel = new Kwps_VersionModel($this->testData['validVersion']);
        $versionModel->save();

        $randomString = substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50) , 1) .substr( md5( time() ), 1);

        $versionModel->setName($randomString);
        $versionModel->save();

        $this->assertEquals($randomString, $versionModel->getName());
    }

    function testDeleteVersion()
    {
        $this->addTestId();

        $versionModel = new Kwps_VersionModel($this->testData['validVersion']);

        $this->assertTrue(method_exists($versionModel, 'delete'));

        $versionModel->delete();

        $this->assertEquals('DEL', $versionModel->getStatus());
    }
}