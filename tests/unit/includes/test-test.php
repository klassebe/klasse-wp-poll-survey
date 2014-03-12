<?php

class TestTest extends Base_UnitTestCase {

    protected $classLocation;
    protected $testModel;

    function setUp() {

        parent::setUp();
        $this->classLocation = plugin_dir_path(__DIR__) . '../../includes/models/test.php';
        //Klasse_WP_Poll_Survey::activate();

        require_once $this->classLocation;

    } // end setup

    function testFileExists() {
        $this->assertTrue(file_exists($this->classLocation));
    }

    function testClass() {
        $testModel = new Kwps_TestModel();
        $this->assertTrue($testModel instanceof Kwps_TestModel);
    }

    function testCreateOnConstruct()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
        $testModel = new Kwps_TestModel();

    }

}