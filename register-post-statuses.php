<?php

add_action( 'init', array('\kwps_classes\duplicate','register_post_status' ));
add_action( 'init', array('\kwps_classes\locked','register_post_status' ));