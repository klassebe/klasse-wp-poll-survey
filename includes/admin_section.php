<?php

namespace includes;
require_once __DIR__ . '/testCollections_list_table.php';
require_once __DIR__ . '/uniqueness.php';


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
                    $test_collection = Test_Collection::get_as_array($current_post->ID);

                    $tests = array($test_collection);

                    $versions = Version::get_all_children($current_post->ID);

                    foreach($versions as $version){
                        $version_as_array = Version::get_as_array($version['ID']);
                        $version_questions = Question::get_all_children($version['ID']);

                        $version_intros = Intro::get_all_children($version['ID']);
                        $version_outros = Outro::get_all_children($version['ID']);

                        $tests = array_merge($tests, array($version_as_array), $version_questions, $version_intros, $version_outros);

                        foreach($version_questions as $question){
                            $version_answer_options = Answer_Option::get_all_children($question['ID']);
                         $tests = array_merge($tests, $version_answer_options);
                        }
                    }
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
            <script>var kwpsPolls=[]</script>
            <?php
        }
        $kwps_uniqueness_options = array(
            'logged_in' => Uniqueness::get_options_for_logged_in_users(),
            'logged_out' => Uniqueness::get_options_for_logged_out_users(),
        );
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