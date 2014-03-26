<?php

namespace includes;
require_once __DIR__ . '/poll_list_table.php';


class admin_section {
    public static function display_form()
    {
        include_once dirname(__FILE__) . '/../views/add.php';

    }

    public static function display_tests() {
        $poll_list = new Poll_List_Table();
        $poll_list->prepare_items();

        include_once __DIR__ . '/../views/poll_list.php';
    }
} 