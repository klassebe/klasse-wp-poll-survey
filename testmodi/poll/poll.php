<?php

class Poll
{
    private static $table_prefix = 'kwps_';

    public function install()
    {
        global $wpdb;

        $tableDefaultPrefix = $wpdb->prefix . self::$table_prefix;

        $status = $wpdb->get_row("SELECT * FROM {$tableDefaultPrefix}status WHERE label='Active'");

        $wpdb->insert(
            $tableDefaultPrefix . 'mode',
            array(
                'name' => 'Poll',
                'description' => 'This is the poll',
                'status_id' => $status->id
            )
        );
    }
}