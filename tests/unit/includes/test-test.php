<?php

class TestTest extends Base_UnitTestCase {

    protected $classLocation;
    protected $testModel;
    protected $testData = array(
        'validTest' => array(
            'name' => 'A demo test',
            'description' => '<h1>Description</h1><p>This is a description as HTML</p>',
            'view_count' => 0,
            'user_id' => 1,
            'mode_id' => 4,
            'status' => 'ACT'
        )
    );

    function setUp() {

        parent::setUp();
        $this->classLocation = plugin_dir_path(__DIR__) . '../../includes/models/test.php';

        require_once $this->classLocation;

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
        $this->assertTrue($testModel->getStatus() == $data['status']);
        $this->assertNull($testModel->getCloseDate());
    }

    function testSaveTest()
    {
        $data = $this->testData['validTest'];
        $testModel = new Kwps_TestModel($data);

        $this->assertTrue(method_exists($testModel, 'save'));
        $this->markTestIncomplete('Not ready');

        $testModel->save();

        $this->assertNotNull($testModel->getId());
        $this->assertNotNull($testModel->getCreateDate());
    }


}