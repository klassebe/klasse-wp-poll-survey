<?php

add_action( 'init', array('\includes\duplicate','register_post_status' ));
add_action( 'init', array('\includes\locked','register_post_status' ));