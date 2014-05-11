<?php

namespace includes;
require_once __DIR__ . '/testCollections_list_table.php';


class admin_section {
    public static function display_form()
    {
        if( isset($_GET['action']) && 'edit' === $_GET['action']){
            if( isset($_GET['id']) ) {
                $current_post = get_post($_GET['id']);

                if( null === $current_post ) {
                    echo 'post not found';
                } elseif ( 'kwps_test_collection' !== $current_post->post_type ) {
                    echo 'post not of type kwps_test_collection';
                } else {
                    $main_poll_as_array = Version::get_as_array($current_post->ID);
                    $main_poll_questions = Question::get_all_children($current_post->ID);

                    $intros = Intro::get_all_children($current_post->ID);
                    $outros = Outro::get_all_children($current_post->ID);

                    $polls = array($main_poll_as_array);
                    $polls = array_merge($polls, $main_poll_questions, $intros, $outros);

                    foreach($main_poll_questions as $question){
                        $answer_options = Answer_Option::get_all_children($question['ID']);
                        $polls = array_merge($polls, $answer_options);
                    }


                    $versions = Version::get_all_children($current_post->ID);

                    foreach($versions as $version){
                        $version_as_array = Version::get_as_array($version['ID']);
                        $version_questions = Question::get_all_children($version['ID']);

                        $version_intros = Intro::get_all_children($version['ID']);
                        $version_outros = Outro::get_all_children($version['ID']);

                        $polls = array_merge($polls, array($version_as_array), $version_questions, $version_intros, $version_outros);

                        foreach($version_questions as $question){
                            $version_answer_options = Answer_Option::get_all_children($question['ID']);
                            $polls = array_merge($polls, $version_answer_options);
                        }
                    }

                    $global_settings = array('kwps_question' => array('min' => 1, 'max' => 1));

                ?>
                    <script>var kwpsTests=<?php echo json_encode($polls); ?></script>
                    <script>var kwpsSettings=<?php echo  $global_settings?></script>
                <?php
                }
            } else {
                echo 'No post id given!';
            }
        } else {
        ?>
            <script>var kwpsPolls=[]</script>
        <?php
        }

        include_once __DIR__ . '/../views/add.php';

    }

    public static function display_tests() {
        $poll_list = new Test_Collections_List_Table();
        $poll_list->prepare_items();

        include_once __DIR__ . '/../views/poll_list.php';
    }
} 