<?php

class TestTest extends Base_UnitTestCase {

    protected $classLocation;
    protected $testModel;
    protected $testData = array(
        'validTest' => array(

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
        $testModel = new Kwps_TestModel();

    }

}