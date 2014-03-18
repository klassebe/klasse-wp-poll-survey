<?php
/**
 * Version model
 *
 * @package Klasse_WP_Poll_Survey
 * @subpackage VersionModel
 * @since 0.1
 */


require_once dirname(__FILE__) . '/../general.php';

class Kwps_TroEntryModel
{
    private static $table_prefix = 'kwps_';

    private $session_id; //VARCHAR
    private $tro_id; //INT
    private $form_key; //VARCHAR
    private $value; //TEXT
    private $create_date; //TIMESTAMP

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

        $id = $data['session_id'];

        $wpdb->replace(
            $tableDefaultPrefix . 'tro_entry',
            $data
        );

        $this->setSessionId($id);
        $this->get();

        return $id;
    }

    public function get()
    {
        global $wpdb;

        $tableDefaultPrefix = $wpdb->prefix . self::$table_prefix;

        $troEntry = $wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}tro_entry WHERE id = {$this->id}");

        if(is_object($troEntry)) {
            foreach (get_object_vars($troEntry) as $name => $value) {
                $this->$name = $value;
            }
        }
    }

    /**
     * @param mixed $create_date
     */
    public function setCreateDate($create_date)
    {
        $this->create_date = $create_date;
    }

    /**
     * @return mixed
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * @param mixed $form_key
     */
    public function setFormKey($form_key)
    {
        $this->form_key = $form_key;
    }

    /**
     * @return mixed
     */
    public function getFormKey()
    {
        return $this->form_key;
    }

    /**
     * @param mixed $session_id
     */
    public function setSessionId($session_id)
    {
        $this->session_id = $session_id;
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->session_id;
    }

    /**
     * @param mixed $tro_id
     */
    public function setTroId($tro_id)
    {
        $this->tro_id = $tro_id;
    }

    /**
     * @return mixed
     */
    public function getTroId()
    {
        return $this->tro_id;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }


}
