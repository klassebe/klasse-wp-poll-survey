<?php

namespace includes;
require_once __DIR__ . '/testCollections_list_table.php';
require_once __DIR__ . '/uniqueness.php';


class admin_section {
    public static function display_form()
    {
        $kwps_uniqueness_options = array(
            'logged_in' => Uniqueness::get_options_for_logged_in_users(),
            'logged_out' => Uniqueness::get_options_for_logged_out_users(),
        );

        $kwps_test_modi = Test_Modus::get_published_modi();

        if( isset($_GET['action']) && 'edit' === $_GET['action']){

            if( isset($_GET['id']) ) {
                $current_post = get_post($_GET['id']);

                if( null === $current_post ) {
                    echo 'post not found';
                } elseif ( 'kwps_test_collection' !== $current_post->post_type ) {
                    echo 'post not of type kwps_test_collection';
                } else {
                    $test_collection = Test_Collection::get_as_array($current_post->ID);

                    $tests = array($test_collection);

                    $versions = Version::get_all_by_post_parent($current_post->ID);

                    $question_groups = array();
                    $questions = array();
                    $intros = array();
                    $outros = array();
                    $answer_options = array();

                    foreach($versions as $version){
                        $question_groups = array_merge($question_groups, Question_Group::get_all_by_post_parent($version['ID']));
                        $intros = array_merge($intros, Intro::get_all_by_post_parent($version['ID']));
                        $outros = array_merge($outros, Outro::get_all_by_post_parent($version['ID']));
                    }

                    foreach($question_groups as $question_group){
                        $questions = array_merge($questions, Question::get_all_by_post_parent($question_group['ID']));
                    }

                    foreach($questions as $question){
                        $answer_options = array_merge($answer_options, Answer_Option::get_all_by_post_parent($question['ID']));
                    }

                    $tests = array_merge(
                        $tests, $versions, $question_groups, $questions, $intros, $outros, $answer_options,
                        $kwps_test_modi
                    );
                ?>
                    <script>var kwpsTests=<?php echo json_encode($tests); ?></script>
                <?php
                }
                ?>
                <script>var kwpsUniquenessTypes=<?php echo json_encode(Uniqueness::get_types()) ?></script>
                <?php
                } else {
                echo 'No post id given!';
            }
        } else {

            ?>
            <script>var kwpsTests=<?php echo json_encode($kwps_test_modi); ?></script>
            <?php
        }

            ?>

        <script>var kwpsUniquenessTypes=<?php echo json_encode($kwps_uniqueness_options) ?></script>
        <script>var kwpsTestModi=<?php echo json_encode(Test_Modus::get_published_modi()) ?></script>
        <?php

        include_once __DIR__ . '/../views/add.php';

    }

    public static function display_tests() {
        $poll_list = new Test_Collections_List_Table();
        $poll_list->prepare_items();

        include_once __DIR__ . '/../views/poll_list.php';
    }
} 