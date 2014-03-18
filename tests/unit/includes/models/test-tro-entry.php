<?php

class TestTroEntry extends Base_UnitTestCase {

    private static $table_prefix = 'kwps_';

    protected $classLocation;
    protected $troModel;
    protected $testData = array(
        'validTro' => array(
            'html_text' => '<h1>Description</h1><p>This is a description as HTML</p><form><input type="text" name="name"></form>',
        ),
        'validTroEntry' => array(
            'session_id' => '',
            'tro_id' => 0,
            'form_key' => 'name',
            'value' => 'Random N@me'
        )
    );

    function setUp() {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;

        parent::setUp();

        require_once plugin_dir_path(__DIR__) . '../../../includes/models/tro-entry.php';
        require_once plugin_dir_path(__DIR__) . '../../../includes/models/tro.php';

        $this->troModel = new Kwps_TroModel($this->testData['validTro']);
        $this->troModel->save();

    } // end setup

    function addTroId()
    {
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $tro = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}tro");

        $this->testData['validTroEntry']['tro_id'] = $tro->id;
        $this->testData['validTroEntry']['session_id'] = substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50) , 1) .substr( md5( time() ), 1);

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
        $this->assertTrue(file_exists(plugin_dir_path(__DIR__) . '../../../includes/models/tro-entry.php'));
    }

    function testClass() {
        $troEntryModel = new Kwps_TroEntryModel();
        $this->assertTrue($troEntryModel instanceof Kwps_TroEntryModel);
    }

    function testCreateOnConstruct()
    {
        $data = $this->testData['validTroEntry'];
        $troEntryModel = new Kwps_TroEntryModel($data);

        $this->assertTrue($troEntryModel->getValue() == $data['value']);
    }

    function testGetSingleTro()
    {
        $this->markTestIncomplete('erezefze');
        $tableDefaultPrefix = $this->wpdb->prefix . self::$table_prefix;
        $this->addTroId();
        $this->wpdb->insert(
            $tableDefaultPrefix . 'tro_entry',
            $this->testData['validTroEntry']
        );

        $troEntryReference = $this->wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}tro_entry");

        $troEntryModel = new Kwps_TroEntryModel();
        $troEntryModel->setSessionId($troEntryReference->session_id);
        $troEntryModel->get();

        $this->assertNotNull($troEntryModel->getSessionId());
        $this->assertEquals($troEntryModel->getSessionId(), $troEntryReference->session_id);
    }

    function testUpdateTroEntry()
    {
        $this->addTroId();
        var_dump($this->testData['validTroEntry']);
        $troEntryModel = new Kwps_TroEntryModel($this->testData['validTroEntry']);
        $troEntryModel->save();

        $randomString = substr( "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50) , 1) .substr( md5( time() ), 1);

        $troEntryModel->setValue($randomString);
        $troEntryModel->save();

        $this->assertEquals($randomString, $troEntryModel->getValue());
    }
}