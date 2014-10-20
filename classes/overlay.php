<?php

namespace kwps_classes;

class Overlay {
    public static function get_result_page(){
        include __DIR__ . '/../views/result_page.php';
        exit();
    }

    public static function get_video_page () {
        include __DIR__ . '/../views/video_page.php';
        exit();
    }
} 