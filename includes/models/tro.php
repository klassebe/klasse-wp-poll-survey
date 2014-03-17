<?php
/**
 * Version model
 *
 * @package Klasse_WP_Poll_Survey
 * @subpackage VersionModel
 * @since 0.1
 */


require_once dirname(__FILE__) . '/../general.php';

class Kwps_TroModel
{
    private static $table_prefix = 'kwps_';

    private $id; //INT
    private $html_text; //TEXT

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
            $tableDefaultPrefix . 'tro',
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

        $tro = $wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}tro WHERE id = {$this->id}");

        if(is_object($tro)) {
            foreach (get_object_vars($tro) as $name => $value) {
                $this->$name = $value;
            }
        }
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
     * @param mixed $html_text
     */
    public function setHtmlText($html_text)
    {
        $this->html_text = $html_text;
    }

    /**
     * @return mixed
     */
    public function getHtmlText()
    {
        return $this->html_text;
    }
}
