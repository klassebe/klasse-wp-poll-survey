<?php

add_action('init', array( '\includes\session', 'myStartSession' ), 1  );
add_action('wp_logout', array( '\includes\session', 'myEndSession' ) );
add_action('wp_login', array( '\includes\session', 'myEndSession' ) );