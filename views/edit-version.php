<?php
$form_action = '?page=' . $_REQUEST['page'] . '&section=edit_version';

if( isset( $_REQUEST['id'] ) ) {
    $version = \kwps_classes\Version::get_as_array( $_REQUEST['id'] );
    $question_groups = \kwps_classes\Question_Group::get_all_by_post_parent( $_REQUEST['id'] );
    foreach( $question_groups as $question_group ) {
        $questions = \kwps_classes\Question::get_all_by_post_parent( $question_group['ID'] );
        foreach( $questions as $question ) {
            $answer_options = \kwps_classes\Answer_Option::get_all_by_post_parent( $question['ID'] );
            foreach( $answer_options as $answer_option ) {
                $question['answer_options'][$answer_option['_kwps_sort_order']] = $answer_option;
            }
            $question_group['questions'][$question['_kwps_sort_order']] = $question;
        }
        $version['question_groups'][$question_group['_kwps_sort_order']] = $question_group;
    }
    $version['intro'] = \kwps_classes\Intro::get_one_by_post_parent( $_REQUEST['id'] );
    $version['outro'] = \kwps_classes\Outro::get_one_by_post_parent( $_REQUEST['id'] );
    $form_action .= '&id=' . $_REQUEST['id'];
} else {
    if( isset( $version_data ) ) {
        $version = $version_data;
    } else {
        $version = array(
            'post_title' => '',
            'post_parent' => $_REQUEST['post_parent'],
            'post_status' => 'draft',
            '_kwps_sort_order' => 1,
            'intro' => array(
                'post_content' => '',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            ),
            'outro' => array(
                'post_content' => '',
                '_kwps_sort_order' => 1,
                'post_status' => 'draft',
            ),
            'question_groups' => array(
                1 => array(
                    '_kwps_sort_order' => 1,
                    'post_status' => 'draft',
                    'post_title' => '',
                    'post_content' => '',
                    'questions' => array(
                        1 => array(
                            '_kwps_sort_order' => 1,
                            'post_status' => 'draft',
                            'post_content' => '',
                            'answer_options' => array(
                                1 => array(
                                    '_kwps_sort_order' => 1,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                                2 => array(
                                    '_kwps_sort_order' => 2,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                            ),
                        ),
                    ),
                ),

            ),
        );
    }
}
?>
<script language="JavaScript">
    var versionData = <?php echo json_encode($version); ?>;
</script>
<div class="wrap">
    <h2>Versie</h2>
    <form action="<?php echo $form_action ?>" method="post" >
        <?php if( isset( $version['ID'] ) ):?>
            <input type="hidden" name="ID" value="<?php echo $version['ID'] ?>" />
        <?php endif;?>
        <input type="hidden" name="_kwps_sort_order" value="<?php echo $version['_kwps_sort_order']; ?>">
        <input type="hidden" name="post_parent" value="<?php echo $version['post_parent']; ?>">
        <input type="hidden" name="post_status" value="<?php echo $version['post_status']; ?>">
        <input
            type="text"
            name="post_title"
            id="kwps-post-title"
            value="<?php echo $version['post_title'] ?>"
            class="<?php if( isset( $version['errors']['post_title'] ) ) echo 'error'; ?>"
        />
        <?php $intro = $version['intro'];?>
        <input type="hidden" name="intro[post_status]" value="<?php if( isset( $intro['post_status'] ) ) echo $intro['post_status'];?>">

        <input
            type="text"
            name="intro[post_content]"
            placeholder="Intro"
            value="<?php echo $intro['post_content'] ?>"
            class="<?php if( isset( $intro['errors']['post_content'] ) )  echo 'error'; ?>"
        />
        <div id="kwps-question-groups">
                <?php foreach( $version['question_groups'] as $question_group ): ?>
                    <div id="kwps-question-group-<?php echo $question_group['_kwps_sort_order'] ?>">
                        <h3>Pagina <?php echo $question_group['_kwps_sort_order'] ?></h3>
                        <?php $question_group_field_index = 'question_groups[' . $question_group['_kwps_sort_order'] .']' ?>
                        <?php if( isset ($question_group['ID'] ) ): ?>
                            <input type="hidden"
                                   name="<?php echo $question_group_field_index; ?>[ID]"
                                   value="<?php echo $question_group['ID'] ?>" />
                        <?php endif;?>
                        <input type="hidden"
                               name="<?php echo $question_group_field_index; ?>[_kwps_sort_order]"
                               value="<?php echo $question_group['_kwps_sort_order'] ?>" />
                        <input type="hidden"
                               name="<?php echo $question_group_field_index; ?>[post_status]"
                               value="<?php echo $question_group['post_status'] ?>" />
                        <input type="text"
                               name="<?php echo $question_group_field_index; ?>[post_title]"
                               value="<?php echo $question_group['post_title'] ?>"
                               class="<?php if( isset( $question_group['errors']['post_title'] ) ) echo 'error'; ?>"
                        />
                        <input type="text"
                               name="<?php echo $question_group_field_index; ?>[post_content]"
                               value="<?php echo $question_group['post_content'] ?>"
                               class="<?php if( isset( $question_group['errors']['post_content'] ) ) echo 'error'; ?>"
                            />
                        <div id="kwps-question-group-questions">
                            <?php foreach( $question_group['questions'] as $question ) : ?>
                                <div id="kwps-question-group-question-<?php echo $question['_kwps_sort_order'] ?>">
                                    <h3>Vraag <?php echo $question['_kwps_sort_order'] ?></h3>
                                    <?php
                                        $question_field_index =
                                            $question_group_field_index . '[questions][' .
                                            $question['_kwps_sort_order'] . ']'
                                    ?>
                                    <?php if( isset ($question['ID'] ) ): ?>
                                        <input type="hidden"
                                               name="<?php echo $question_field_index; ?>[ID]"
                                               value="<?php echo $question['ID'] ?>" />
                                    <?php endif;?>
                                    <input
                                        type="hidden"
                                        name="<?php echo $question_field_index;?>[_kwps_sort_order]"
                                        value="<?php echo $question['_kwps_sort_order'];?>"
                                    /><input
                                        type="hidden"
                                        name="<?php echo $question_field_index;?>[post_status]"
                                        value="<?php echo $question['post_status'];?>"
                                        />
                                    <input
                                        type="text"
                                        name="<?php echo $question_field_index;?>[post_content]"
                                        value="<?php echo $question['post_content'];?>"
                                        class="<?php if( isset( $question['errors']['post_content'] ) ) echo 'error'; ?>"
                                        />
                                <div id="kwps-question-group-question-answer-options">
                                    <?php foreach( $question['answer_options'] as $answer_option ): ?>
                                        <div id="kwps-question-group-question-answer-option-<?php echo $answer_option['_kwps_sort_order'] ?>">
                                            <h3>Antwoord <?php echo $answer_option['_kwps_sort_order'] ;?></h3>
                                            <?php
                                                $answer_option_field_index = $question_field_index .
                                                    '[answer_options][' . $answer_option['_kwps_sort_order'] . ']';
                                            ?>
                                            <?php if( isset ($answer_option['ID'] ) ): ?>
                                                <input type="hidden"
                                                       name="<?php echo $answer_option_field_index; ?>[ID]"
                                                       value="<?php echo $answer_option['ID'] ?>" />
                                            <?php endif;?>
                                            <input
                                                type="hidden"
                                                name="<?php echo $answer_option_field_index;?>[_kwps_sort_order]"
                                                value="<?php echo $answer_option['_kwps_sort_order']; ?>"
                                            />
                                            <input
                                                type="hidden"
                                                name="<?php echo $answer_option_field_index;?>[post_status]"
                                                value="<?php echo $answer_option['post_status']; ?>"
                                                />
                                            <input
                                                type="text"
                                                name="<?php echo $answer_option_field_index;?>[post_content]"
                                                value="<?php echo $answer_option['post_content']; ?>"
                                                class="<?php if( isset( $answer_option['errors']['post_content'] ) ) echo 'error'; ?>"
                                                />
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php $outro = $version['outro']; ?>
        <input type="hidden" name="outro[post_status]" value="<?php if( isset( $outro['post_status'] ) ) echo $outro['post_status'];?>">
        <input
            type="text"
            name="outro[post_content]"
            placeholder="Outro"
            value="<?php echo $outro['post_content'] ?>"
            class="<?php if( isset( $outro['errors']['post_content'] ) ) echo 'error'; ?>"
            />
        <button type="submit">Wijzigingen opslaan</button>
    </form>
</div>
