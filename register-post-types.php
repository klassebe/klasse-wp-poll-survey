<?php

add_action('init', array('\includes\version','register_post_type'));
add_action('init', array('\includes\answer_option','register_post_type'));
add_action('init', array('\includes\question','register_post_type'));
add_action('init', array('\includes\question_group','register_post_type'));
add_action('init', array('\includes\entry','register_post_type'));
add_action('init', array('\includes\intro','register_post_type'));
add_action('init', array('\includes\intro_result','register_post_type'));
add_action('init', array('\includes\outro','register_post_type'));
add_action('init', array('\includes\test_modus','register_post_type'));
add_action('init', array('\includes\test_collection','register_post_type'));
add_action('init', array('\includes\result_profile','register_post_type'));
add_action('init', array('\includes\result_group','register_post_type'));
add_action('init', array('\includes\coll_outro','register_post_type'));