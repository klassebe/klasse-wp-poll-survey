<?php
/**
 * Version model
 *
 * @package Klasse_WP_Poll_Survey
 * @subpackage VersionModel
 * @since 0.1
 */


require_once dirname(__FILE__) . '/../general.php';

class Kwps_VersionModel
{
    private static $table_prefix = 'kwps_';

    private $id; //INT
    private $test_id; //INT
    private $intro_id; //INT
    private $outro_id; //INT
    private $name; //VARCHAR
    private $status = 'ACT'; //VARCHAR
    private $api_key; //VARCHAR

    public function __construct($attributes = array()) {
        if(count($attributes) == 0) {
            return;
        }

        foreach($attributes as $attribute => $value) {
            $attributeName = 'set' . General::camelCase($attribute);

            $this->$attributeName($value);
        }
    }

    public function save() {
        global $wpdb;
        $tableDefaultPrefix = $wpdb->prefix . self::$table_prefix;

        $data = array();
        foreach (get_object_vars($this) as $name => $value) {
            if($value) {
                $data[$name] = $value;
            }
        }

        $id = $wpdb->replace(
            $tableDefaultPrefix . 'version',
            $data
        );

        $this->setId($id);
        $this->get();

        return $id;
    }

    public function get()
    {
        global $wpdb;

        $tableDefaultPrefix = $wpdb->prefix . self::$table_prefix;

        $version = $wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}version WHERE id = {$this->id}");

        if(is_object($version)) {
            foreach (get_object_vars($version) as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    public function delete()
    {
        $this->setStatus('DEL');
        $this->save();
    }

    /**
     * @param mixed $api_key
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->api_key;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $intro_id
     */
    public function setIntroId($intro_id)
    {
        $this->intro_id = $intro_id;
    }

    /**
     * @return mixed
     */
    public function getIntroId()
    {
        return $this->intro_id;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $outro_id
     */
    public function setOutroId($outro_id)
    {
        $this->outro_id = $outro_id;
    }

    /**
     * @return mixed
     */
    public function getOutroId()
    {
        return $this->outro_id;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $table_prefix
     */
    public static function setTablePrefix($table_prefix)
    {
        self::$table_prefix = $table_prefix;
    }

    /**
     * @return string
     */
    public static function getTablePrefix()
    {
        return self::$table_prefix;
    }

    /**
     * @param mixed $test_id
     */
    public function setTestId($test_id)
    {
        $this->test_id = $test_id;
    }

    /**
     * @return mixed
     */
    public function getTestId()
    {
        return $this->test_id;
    }


}
