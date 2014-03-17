<?php

class TestTro extends Base_UnitTestCase {

    private static $table_prefix = 'kwps_';

    protected $classLocation;
    protected $troModel;
    protected $testData = array(
        'validTro' => array(
            'html_text' => '<h1>Description</h1><p>This is a description as HTML</p>',
        )
    );

    function setUp() {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;

        parent::setUp();

        require_once plugin_dir_path(__DIR__) . '../../../includes/models/tro.php';
        require_once plugin_dir_path(__DIR__) . '../../../includes/models/test.php';
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
        $this->assertTrue(file_exists(plugin_dir_path(__DIR__) . '../../../includes/models/tro.php'));
    }

    function testClass() {
        $troModel = new Kwps_TroModel();
        $this->assertTrue($troModel instanceof Kwps_TroModel);
    }

    function testCreateOnConstruct()
    {
        $data = $this->testData['validTro'];
        $troModel = new Kwps_TroModel($data);

        $this->assertTrue($troModel->getHtmlText() == $data['html_text']);
    }

    function testGetSingleTro()
    {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $this->wpdb->insert(
            $tableDefaultPrefix . 'tro',
            $this->testData['validTro']
        );

        $troReference = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}tro");

        $troModel = new Kwps_TroModel();
        $troModel->setId($troReference->id);
        $troModel->get();

        $this->assertNotNull($troModel->getId());
        $this->assertEquals($troModel->getId(), $troReference->id);
    }

    function testGetHtmlText()
    {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $this->wpdb->insert(
            $tableDefaultPrefix . 'tro',
            $this->testData['validTro']
        );

        $troReference = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}tro");

        $troModel = new Kwps_TroModel();
        $troModel->setId($troReference->id);
        $troModel->get();

        $this->assertNotNull($troModel->getId());
        $this->assertEquals($troModel->getHtmlText(), $troReference->html_text);
    }

    function testUpdateTro()
    {
        $troModel = new Kwps_TroModel($this->testData['validTro']);
        $troModel->save();

        $randomString = substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50) , 1) .substr( md5( time() ), 1);

        $troModel->setHtmlText($randomString);
        $troModel->save();

        $this->assertEquals($randomString, $troModel->getHtmlText());
    }
}