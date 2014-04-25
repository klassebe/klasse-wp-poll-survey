<?php

namespace includes;

require_once 'intro.php';

class Outro extends Intro{

    public static $label = 'kwps-outro';
    public static $rewrite = array(
            'slug' => 'intros',
            'with_front' => false,
        );

    public static $post_type = 'kwps_outro';
}