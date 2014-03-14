<?php
/**
 * Test model
 *
 * @package Klasse_WP_Poll_Survey
 * @subpackage TestModel
 * @since 0.1
 */


require_once dirname(__FILE__) . '/../general.php';

class Kwps_TestModel
{
    private static $table_prefix = 'kwps_';

    private $id; //INT
    private $name; //VARCHAR
    private $description; //TEXT
    private $view_count; //INT
    private $create_date; //TIMESTAMP
    private $update_date; //TIMESTAMP
    private $publish_date; //TIMESTAMP
    private $close_date; //TIMESTAMP
    private $user_id; //INT
    private $mode_id; //INT
    private $status; //VARCHAR

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

        $wpdb->insert(
            $tableDefaultPrefix . 'test',
            array(
                'name' => 'Poll',
                'description' => 'This is the poll'
            )
        );
    }

    /**
     * @param mixed $close_date
     */
    public function setCloseDate($close_date)
    {
        $this->close_date = $close_date;
    }

    /**
     * @return mixed
     */
    public function getCloseDate()
    {
        return $this->close_date;
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
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
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
     * @param mixed $mode_id
     */
    public function setModeId($mode_id)
    {
        $this->mode_id = $mode_id;
    }

    /**
     * @return mixed
     */
    public function getModeId()
    {
        return $this->mode_id;
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
     * @param mixed $publish_date
     */
    public function setPublishDate($publish_date)
    {
        $this->publish_date = $publish_date;
    }

    /**
     * @return mixed
     */
    public function getPublishDate()
    {
        return $this->publish_date;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $update_date
     */
    public function setUpdateDate($update_date)
    {
        $this->update_date = $update_date;
    }

    /**
     * @return mixed
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $view_count
     */
    public function setViewCount($view_count)
    {
        $this->view_count = $view_count;
    }

    /**
     * @return mixed
     */
    public function getViewCount()
    {
        return $this->view_count;
    }


}
