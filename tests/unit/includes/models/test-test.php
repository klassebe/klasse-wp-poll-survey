<?php

class TestTest extends Base_UnitTestCase {

    private static $table_prefix = 'kwps_';

    protected $classLocation;
    protected $testModel;
    protected $testData = array(
        'validTest' => array(
            'name' => 'A demo test',
            'description' => '<h1>Description</h1><p>This is a description as HTML</p>',
            'view_count' => 0,
            'user_id' => 0,
            'mode_id' => 0,
        ),
        'validTestFull' => array(
            'name' => 'A FULL test ',
            'description' => '<h1>Description</h1><p>This is a description as HTML</p>',
            'view_count' => 0,
            'user_id' => 0,
            'mode_id' => 0,
            'status' => 'ACT'
        )
    );

    function setUp() {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;

        parent::setUp();
        $this->classLocation = plugin_dir_path(__DIR__) . '../../../includes/models/test.php';

        require_once $this->classLocation;


        $this->kwps->addTestModi();
        $mode = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}mode");
        $user = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}users");

        $this->testData['validTest']['mode_id'] = $mode->id;
        $this->testData['validTest']['user_id'] = $user->ID;
        $this->testData['validTestFull']['publish_date'] = date('Y-m-d H:i:s', strtotime("yesterday"));
        $this->testData['validTestFull']['close_date'] = date('Y-m-d H:i:s', strtotime("tomorrow"));
        $this->testData['validTestFull']['update_date'] = date('Y-m-d H:i:s', strtotime("+1 hour"));
        $this->testData['validTestFull']['create_date'] = date('Y-m-d H:i:s', time());

    } // end setup


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
        $this->assertTrue(file_exists($this->classLocation));
    }

    function testClass() {
        $testModel = new Kwps_TestModel();
        $this->assertTrue($testModel instanceof Kwps_TestModel);
    }

    function testCreateOnConstruct()
    {
        $data = $this->testData['validTest'];
        $testModel = new Kwps_TestModel($data);

        $this->assertTrue($testModel->getName() == $data['name']);
        $this->assertTrue($testModel->getDescription() == $data['description']);
        $this->assertTrue($testModel->getViewCount() == $data['view_count']);
        $this->assertTrue($testModel->getUserId() == $data['user_id']);
        $this->assertTrue($testModel->getModeId() == $data['mode_id']);
        $this->assertTrue($testModel->getStatus() == 'ACT');
        $this->assertNull($testModel->getCloseDate());


        $data = $this->testData['validTestFull'];
        $testModel = new Kwps_TestModel($data);

        $this->assertNotNull($testModel->getCreateDate());
        $this->assertEquals($testModel->getUpdateDate(), $data['update_date']);
        $this->assertEquals($testModel->getPublishDate(), $data['publish_date']);
        $this->assertEquals($testModel->getCloseDate(), $data['close_date']);
        $this->assertEquals($testModel->getStatus(), $data['status']);
    }

    function testSaveTest()
    {
        $testModel = new Kwps_TestModel($this->testData['validTest']);

        $this->assertTrue(method_exists($testModel, 'save'));

        $resultOfSave = $testModel->save();

        $this->assertTrue(is_int($resultOfSave));
        $this->assertNotNull($testModel->getId());
        $this->assertEquals($resultOfSave, $testModel->getId());
        $this->assertNotNull($testModel->getCreateDate());
        $this->assertNotNull($testModel->getUpdateDate());
        $this->assertNotNull($testModel->getPublishDate());
        $this->assertNotNull($testModel->getCLoseDate());

    }

    function testGetSingleTest()
    {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $this->wpdb->insert(
            $tableDefaultPrefix . 'test',
            $this->testData['validTest']
        );

        $testReference = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}test");

        $testModel = new Kwps_TestModel();
        $testModel->setId($testReference->id);
        $testModel->get();

        $this->assertNotNull($testModel->getId());
        $this->assertEquals($testModel->getId(), $testReference->id);
        $this->assertNotNull($testModel->getCreateDate());
    }

    function testUpdateTest()
    {
        $testModel = new Kwps_TestModel($this->testData['validTest']);
        $testModel->save();

        $randomString = substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50) , 1) .substr( md5( time() ), 1);

        $testModel->setName($randomString);
        $testModel->save();

        $this->assertEquals($randomString, $testModel->getName());
    }

    function testDeleteTest()
    {
        $testModel = new Kwps_TestModel($this->testData['validTest']);

        $this->assertTrue(method_exists($testModel, 'delete'));

        $testModel->delete();

        $this->assertEquals('DEL', $testModel->getStatus());
    }
}