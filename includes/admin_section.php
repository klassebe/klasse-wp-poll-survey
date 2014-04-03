<?php

namespace includes;
require_once __DIR__ . '/poll_list_table.php';


class admin_section {
    public static function display_form()
    {
        if( isset($_GET['action']) && 'edit' === $_GET['action']){
            if( isset($_GET['id']) ) {
                $current_post = get_post($_GET['id']);

                if( null === $current_post ) {
                    echo 'post not found';
                } elseif ( 'kwps_poll' !== $current_post->post_type ) {
                    echo 'post not of type kwps_poll';
                } else {
                    $post_as_array = (array) $current_post;

                    $post_as_array = kwps_get_post_with_versions($post_as_array);

                    $versions = kwps_get_versions_of_poll($current_post->ID);
                    $answer_option_of_parent = kwps_get_answer_options_of_poll($current_post->ID);


                    $answer_options = kwps_get_answer_options_of_versions($versions);
//                    var_dump($answer_options);

                    $answer_options = array_merge($answer_options, $answer_option_of_parent);
                ?>
                    <script>var parentPost=<?php echo json_encode($post_as_array); ?></script>
                    <script>var answerOptions=<?php echo json_encode($answer_options); ?></script>
                <?php
                }
            } else {
                echo 'No post id given!';
            }
        }

        include_once __DIR__ . '/../views/add.php';

    }

    public static function display_tests() {
        $poll_list = new Poll_List_Table();
        $poll_list->prepare_items();

        include_once __DIR__ . '/../views/poll_list.php';
    }
} 