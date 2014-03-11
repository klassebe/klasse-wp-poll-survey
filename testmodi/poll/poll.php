<?php

class Poll
{
    private static $table_prefix = 'kwps_';

    public function install()
    {
        global $wpdb;

        $tableDefaultPrefix = $wpdb->prefix . self::$table_prefix;

        $wpdb->insert(
            $tableDefaultPrefix . 'mode',
            array(
                'name' => 'Poll',
                'description' => 'This is the poll'
            )
        );
    }
}