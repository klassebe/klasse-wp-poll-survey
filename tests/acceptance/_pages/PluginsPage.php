<?php

namespace pages;

class PluginsPage {
    static $URL = '/wp-admin/plugins.php';

    static $activate = 'Activate';
    static $activate_kwps_selector = "span.activate a[href^='plugins.php?action=activate&plugin=klasse-wp-poll-survey']";
    static $deactivate = 'Deactivate';
    static $deactivate_kwps_selector = '#klasse-wordpress-poll-survey span.deactivate a';
}