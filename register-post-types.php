<?php

add_action('init', array('\kwps_classes\version','register_post_type'));
add_action('init', array('\kwps_classes\answer_option','register_post_type'));
add_action('init', array('\kwps_classes\question','register_post_type'));
add_action('init', array('\kwps_classes\question_group','register_post_type'));
add_action('init', array('\kwps_classes\entry','register_post_type'));
add_action('init', array('\kwps_classes\intro','register_post_type'));
add_action('init', array('\kwps_classes\intro_result','register_post_type'));
add_action('init', array('\kwps_classes\outro','register_post_type'));
add_action('init', array('\kwps_classes\test_modus','register_post_type'));
add_action('init', array('\kwps_classes\test_collection','register_post_type'));
add_action('init', array('\kwps_classes\result_profile','register_post_type'));
add_action('init', array('\kwps_classes\result_group','register_post_type'));
add_action('init', array('\kwps_classes\coll_outro','register_post_type'));