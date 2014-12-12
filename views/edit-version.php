<?php
$form_action = '?page=' . $_REQUEST['page'] . '&section=edit_version';

if( isset( $_REQUEST['id'] ) ) {
    if( isset( $_REQUEST['update'] ) && 'true' == $_REQUEST['update'] ) {
        $version = $version_data;
    } else {
        $version = \kwps_classes\Version::get_with_all_children( $_REQUEST['id'] );
    }
    $form_action .= '&id=' . $_REQUEST['id'] . '&update=true';
} else {
    if( isset( $version_data ) ) {
        $version = $version_data;
    } else {
        $version = array(
            'post_title' => '',
            'post_parent' => $_REQUEST['post_parent'],
            'post_status' => 'draft',
            '_kwps_sort_order' => 0,
            'intro' => array(
                'post_content' => '',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
            ),
            'intro_result' => array(
                'post_content' => '',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
            ),
            'outro' => array(
                'post_content' => '',
                '_kwps_sort_order' => 0,
                'post_status' => 'draft',
            ),
            'question_groups' => array(
                array(
                    '_kwps_sort_order' => 0,
                    'post_status' => 'draft',
                    'post_title' => '',
                    'post_content' => '',
                    'questions' => array(
                        array(
                            '_kwps_sort_order' => 0,
                            'post_status' => 'draft',
                            'post_content' => '',
                            'answer_options' => array(
                                array(
                                    '_kwps_sort_order' => 0,
                                    'post_content' => '',
                                    'post_status' => 'draft',
                                ),
                                array(
                                    '_kwps_sort_order' => 1,
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
    var testModus = <?php echo json_encode($test_modus = \kwps_classes\Test_Collection::get_test_modus( $version['post_parent'] ) ) ?>;
    var versionData = <?php echo json_encode($version); ?>;
</script>
<div class="wrap">
    <h2>Versie</h2>
    <?php if( isset( $test_modus_errors ) && sizeof( $test_modus_errors ) > 0 ):?>
        <div id="test-modus-errors">
        <?php foreach( $test_modus_errors as $rule => $message ) :?>
            <div class="test-modus-error"><?php echo $message ?></div>
        <?php endforeach; ?>
        </div>
    <?php endif;?>
    <form id="edit-version" action="<?php echo $form_action ?>" method="post" class="kwps-form">
        <div class="kwps kwps-single" id="kwps-version">
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
        </div>
        <div class="kwps kwps-single" id="kwps-intro">
            <?php $intro = $version['intro'];?>
            <input type="hidden" name="ID" value="<?php if( isset( $intro['ID'] ) ) echo $intro['ID'];?>">
            <input type="hidden" name="post_status" value="<?php if( isset( $intro['post_status'] ) ) echo $intro['post_status'];?>">
            <input type="hidden" name="post_content" value="<?php echo $intro['post_content'];?>">

            <div class="kwps-content<?php if( isset( $intro['errors']['post_content'] ) )  echo ' error'; ?>">
                <div style="display: none" class="kwps-content-editor">
                    <?php wp_editor( $intro['post_content'], 'intro', array('teeny' => true ) ); ?>
                    <button class="kwps-content-editor-save">Save</button>
                </div>
                <div class="kwps-content-view">
                    <div class="kwps-content-view-content">
                        <?php echo $intro['post_content'] ?>
                    </div>
                    <a class="kwps-content-edit">Edit</a>
                </div>
            </div>

        </div>
        <div class="kwps kwps-single" id="kwps-intro_result">
            <?php $intro = $version['intro_result'];?>
            <input type="hidden" name="ID" value="<?php if( isset( $intro['ID'] ) ) echo $intro['ID'];?>">
            <input type="hidden" name="post_status" value="<?php if( isset( $intro['post_status'] ) ) echo $intro['post_status'];?>">
            <input type="hidden" name="post_content" value="<?php echo $intro['post_content'];?>">

            <div class="kwps-content<?php if( isset( $intro['errors']['post_content'] ) )  echo ' error'; ?>">
                <div style="display: none" class="kwps-content-editor">
                    <?php wp_editor( $intro['post_content'], 'post_content_intro_result', array('teeny' => true ) ); ?>
                    <button class="kwps-content-editor-save">Save</button>
                </div>
                <div class="kwps-content-view">
                    <div class="kwps-content-view-content">
                        <?php echo $intro['post_content'] ?>
                    </div><a class="kwps-content-edit">Edit</a>
                </div>
            </div>
        </div>
        <div id="kwps-question_groups" class="kwps kwps-multi kwps-question_groups">
                <?php foreach( $version['question_groups'] as $question_group_key => $question_group ): ?>
                    <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>" class="kwps-question_group">
                        <h3>Pagina <?php echo $question_group['_kwps_sort_order'] ?></h3>
                        <?php $question_group_field_index = 'question_groups[' . $question_group['_kwps_sort_order'] .']' ?>
                        <?php if( isset ($question_group['ID'] ) ): ?>
                            <input type="hidden"
                                   name="ID"
                                   value="<?php echo $question_group['ID'] ?>" />
                        <?php endif;?>
                        <input type="hidden"
                               name="post_status"
                               value="<?php echo $question_group['post_status'] ?>" />
                        <input type="text"
                               name="post_title"
                               value="<?php echo $question_group['post_title'] ?>"
                               class="<?php if( isset( $question_group['errors']['post_title'] ) ) echo 'error'; ?>"
                        />

                        <input type="hidden" name="post_content" value="<?php echo $question_group['post_content'];?>">

                        <div class="kwps-content<?php if( isset( $question_group['errors']['post_content'] ) )  echo ' error'; ?>">
                            <div style="display: none" class="kwps-content-editor">
                                <?php wp_editor( $question_group['post_content'], 'question_group_' . $question_group_key, array('teeny' => true ) ); ?>
                                <button class="kwps-content-editor-save">Save</button>
                            </div>
                            <div class="kwps-content-view">
                                <div class="kwps-content-view-content">
                                    <?php echo $question_group['post_content'] ?>
                                </div><a class="kwps-content-edit">Edit</a>
                            </div>
                        </div>

                        <div id="kwps-question_group-questions" class="kwps kwps-multi kwps-questions" questionGroupIndex="<?php echo $question_group['_kwps_sort_order'];?>"
                            >
                            <?php foreach( $question_group['questions'] as $question ) : ?>
                                <div id="kwps-question_group-question-<?php echo $question['_kwps_sort_order'] ?>" class="kwps kwps-multi kwps-question"
                                     questionGroupIndex="<?php echo $question_group['_kwps_sort_order'];?>"
                                     questionIndex="<?php echo $question['_kwps_sort_order'];?>"
                                    >
                                    <h3>Vraag <span><?php echo $question['_kwps_sort_order'] ?></span></h3>
                                    <?php if( isset ($question['ID'] ) ): ?>
                                        <input type="hidden"
                                               name="ID"
                                               value="<?php echo $question['ID'] ?>" />
                                    <?php endif;?>
                                    <input
                                        type="hidden"
                                        name="post_status"
                                        value="<?php echo $question['post_status'];?>"
                                        />
                                    <input
                                        type="text"
                                        name="post_content"
                                        value="<?php echo $question['post_content'];?>"
                                        class="<?php if( isset( $question['errors']['post_content'] ) ) echo 'error'; ?>"
                                        />
                                <div id="kwps-question_group-question-answer-options" class="kwps kwps-multi kwps-answer_options" questionGroupIndex="<?php echo $question_group['_kwps_sort_order'];?>"
                                     questionIndex="<?php echo $question['_kwps_sort_order'];?>">
                                    <?php foreach( $question['answer_options'] as $answer_option ): ?>
                                        <div id="kwps-question_group-question-answer_option-<?php echo $answer_option['_kwps_sort_order'] ?>" class="kwps-answer_option">
                                            <h3>Antwoord <span><?php echo $answer_option['_kwps_sort_order'] ;?></span></h3>
                                            <?php if( isset ($answer_option['ID'] ) ): ?>
                                                <input type="hidden"
                                                       name="ID"
                                                       value="<?php echo $answer_option['ID'] ?>" />
                                            <?php endif;?>
                                            <input
                                                type="hidden"
                                                name="post_status"
                                                value="<?php echo $answer_option['post_status']; ?>"
                                                />
                                            <input
                                                type="text"
                                                name="post_content"
                                                value="<?php echo $answer_option['post_content']; ?>"
                                                class="<?php if( isset( $answer_option['errors']['post_content'] ) ) echo 'error'; ?>"
                                                />
                                        </div>
                                    <?php endforeach; ?>
                                    <button class="kwps-create-item">
                                        + AO
                                    </button>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <button class="kwps-create-item">
                                + Q
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <button class="kwps-create-item">
                + QG
            </button>
            </div>
        <div class="kwps kwps-single" id="kwps-outro">
            <?php $outro = $version['outro']; ?>
            <input type="hidden" name="ID" value="<?php if( isset( $outro['ID'] ) ) echo $outro['ID'];?>">
            <input type="hidden" name="post_status" value="<?php if( isset( $outro['post_status'] ) ) echo $outro['post_status'];?>">
            <input
                type="text"
                name="post_content"
                placeholder="Outro"
                value="<?php echo $outro['post_content'] ?>"
                class="<?php if( isset( $outro['errors']['post_content'] ) ) echo 'error'; ?>"
                />
        </div>
        <button id="version-save" type="submit">Wijzigingen opslaan</button>
    </form>
</div>
