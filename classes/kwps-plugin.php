<?php

namespace kwps_classes;

require_once __DIR__ . '/post-types/test-modus.php';

class Kwps_Plugin {

    public static function on_activate(){
        Test_Modus::create_default_test_modi();
    }

    public static function on_deactivate(){

    }
} 