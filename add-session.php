<?php

add_action( 'init', array('\kwps_classes\uniqueness','set_cookie' ));
add_action('init', array( '\kwps_classes\session', 'myStartSession' ), 1  );
add_action('wp_logout', array( '\kwps_classes\session', 'myEndSession' ) );
add_action('wp_login', array( '\kwps_classes\session', 'myEndSession' ) );