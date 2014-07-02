<?php

add_action( 'wp_ajax_kwps_save_test_collection', array('\includes\test_collection','save_from_request'));
add_action( 'wp_ajax_kwps_update_test_collection', array('\includes\test_collection','update_from_request'));
add_action( 'wp_ajax_kwps_delete_test_collection', array('\includes\test_collection','delete_from_request'));

add_action( 'wp_ajax_kwps_save_version', array('\includes\version','save_from_request'));
add_action( 'wp_ajax_kwps_update_version', array('\includes\version','update_from_request'));
add_action( 'wp_ajax_kwps_delete_version', array('\includes\version','delete_from_request'));


add_action( 'wp_ajax_kwps_validate_version', array('\includes\version','ajax_validate_for_publish'));

add_action( 'wp_ajax_kwps_save_question_group', array('\includes\question_group','save_from_request'));
add_action( 'wp_ajax_kwps_update_question_group', array('\includes\question_group','update_from_request'));
add_action( 'wp_ajax_kwps_delete_question_group', array('\includes\question_group','delete_from_request'));

add_action( 'wp_ajax_kwps_save_result_profile', array('\includes\result_profile','save_from_request'));
add_action( 'wp_ajax_kwps_update_result_profile', array('\includes\result_profile','update_from_request'));
add_action( 'wp_ajax_kwps_delete_result_profile', array('\includes\result_profile','delete_from_request'));

add_action( 'wp_ajax_kwps_save_question', array('\includes\question','save_from_request'));
add_action( 'wp_ajax_kwps_update_question', array('\includes\question','update_from_request'));
add_action( 'wp_ajax_kwps_delete_question', array('\includes\question','delete_from_request'));

add_action( 'wp_ajax_kwps_save_answer_option', array('\includes\answer_option','save_from_request'));
add_action( 'wp_ajax_kwps_update_answer_option', array('\includes\answer_option','update_from_request'));
add_action( 'wp_ajax_kwps_delete_answer_option', array('\includes\answer_option','delete_from_request'));

add_action( 'wp_ajax_kwps_save_intro', array('\includes\intro','save_from_request'));
add_action( 'wp_ajax_kwps_update_intro', array('\includes\intro','update_from_request'));
add_action( 'wp_ajax_kwps_delete_intro', array('\includes\intro','delete_from_request'));

add_action( 'wp_ajax_kwps_save_intro_result', array('\includes\intro_result','save_from_request'));
add_action( 'wp_ajax_kwps_update_intro_result', array('\includes\intro_result','update_from_request'));
add_action( 'wp_ajax_kwps_delete_intro_result', array('\includes\intro_result','delete_from_request'));

add_action( 'wp_ajax_kwps_save_outro', array('\includes\outro','save_from_request'));
add_action( 'wp_ajax_kwps_update_outro', array('\includes\outro','update_from_request'));

add_action( 'wp_ajax_kwps_save_coll_outro', array('\includes\coll_outro','save_from_request'));
add_action( 'wp_ajax_kwps_update_coll_outro', array('\includes\coll_outro','update_from_request'));

// nopriv prefix to make sure this function is callable for unregistered users
add_action( 'wp_ajax_nopriv_kwps_save_entry', array('\includes\entry','save_from_request'));
add_action( 'wp_ajax_kwps_save_entry', array('\includes\entry','save_from_request'));
add_action( 'wp_ajax_kwps_delete_entries_from_version', array('\includes\entry','delete_from_version'));

add_action( 'wp_ajax_kwps_save_result_group', array('\includes\result_group','save_from_request'));
add_action( 'wp_ajax_nopriv_kwps_save_result_group', array('\includes\result_group','save_from_request'));

add_action( 'wp_ajax_kwps_get_result_of_version_by_entry_id', array('\includes\result','get_result_of_version_by_entry_id'));
add_action( 'wp_ajax_nopriv_kwps_get_result_of_version_by_entry_id', array('\includes\result','get_result_of_version_by_entry_id'));

add_action( 'wp_ajax_kwps_get_result_of_version', array('\includes\result','get_result_of_version_from_request'));
add_action( 'wp_ajax_nopriv_kwps_get_result_of_version', array('\includes\result','get_result_of_version_from_request'));

add_action( 'wp_ajax_kwps_get_result_of_test_collection',
    array('\includes\result','ajax_get_result_data_of_test_collection'));

add_action( 'wp_ajax_kwps_get_result_profile', array('\includes\result_profile','ajax_get_by_entry_id'));
add_action( 'wp_ajax_nopriv_kwps_get_result_profile', array('\includes\result_profile','ajax_get_by_entry_id'));

add_action( 'wp_ajax_kwps_get_result_page', array('\includes\overlay','get_result_page') );
add_action( 'wp_ajax_kwps_get_video_page', array('\includes\overlay','get_video_page') );