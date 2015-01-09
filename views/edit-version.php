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
        <div id="test-modus-errors" class="error form-invalid below-h2">
        <?php foreach( $test_modus_errors as $rule => $message ) :?>
            <p class=""><?php echo $message ?></p>
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
            <div class="titlediv">
                <div class="titlewrap">
                    <label class="screen-reader-text" id="title-prompt-text" for="kwps-post-title">Enter title here</label>
                    <input
                        type="text"
                        name="post_title"
                        id="kwps-post-title"
                        size="30"
                        spellcheck="true" 
                        autocomplete="off"
                        value="<?php echo $version['post_title'] ?>"
                        class="<?php if( isset( $version['errors']['post_title'] ) ) echo 'kwps_error'; ?> kwps-post-title"
                    />
                </div>
            </div>
        </div>
        <div class="kwps kwps-single<?php if( isset( $intro['errors']['post_content'] ) )  echo ' kwps_error'; ?>" id="kwps-intro">
            <h3>Intro</h3>
            <div class="inside">
                <?php $intro = $version['intro'];?>
                <input type="hidden" name="ID" value="<?php if( isset( $intro['ID'] ) ) echo $intro['ID'];?>">
                <input type="hidden" name="post_status" value="<?php if( isset( $intro['post_status'] ) ) echo $intro['post_status'];?>">

                <textarea style="display: none" name="post_content"><?php echo (isset($intro['post_content']))? $intro['post_content'] : "Intro" ?></textarea>
                <div class="kwps-content<?php if( isset( $intro['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                    <div style="display: none" class="kwps-content-editor">
                        <?php wp_editor( (isset($intro['post_content']))? $intro['post_content'] : "Intro", 'intro', array('teeny' => true ) ); ?>
                        <button class="kwps-content-editor-save button">Save</button>
                    </div>
                    <div class="kwps-content-view">
                        <div class="kwps-content-view-content">
                            <?php echo (isset($intro['post_content']))? $intro['post_content'] : "Intro"  ?>
                        </div>
                        <a class="kwps-content-edit button">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="kwps kwps-single<?php if( isset( $intro['errors']['post_content'] ) )  echo ' kwps_error'; ?>" id="kwps-intro_result">
            <h3>Intro result</h3>
            <div class="inside">
                <?php $intro = $version['intro_result'];?>
                <input type="hidden" name="ID" value="<?php if( isset( $intro['ID'] ) ) echo $intro['ID'];?>">
                <input type="hidden" name="post_status" value="<?php if( isset( $intro['post_status'] ) ) echo $intro['post_status'];?>">
                <textarea style="display: none" name="post_content"><?php echo (isset($intro['post_content']))? $intro['post_content'] : "Intro Result" ?></textarea>

                <div class="kwps-content<?php if( isset( $intro['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                    <div style="display: none" class="kwps-content-editor">
                        <?php wp_editor( (isset($intro['post_content']))? $intro['post_content'] : "Intro Result", 'post_content_intro_result', array('teeny' => true ) ); ?>
                        <button class="kwps-content-editor-save button">Save</button>
                    </div>
                    <div class="kwps-content-view">
                        <div class="kwps-content-view-content">
                            <?php echo (isset($intro['post_content']))? $intro['post_content'] : "Intro Result" ?>
                        </div><a class="kwps-content-edit button">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="kwps-question_groups" class="kwps kwps-multi kwps-question_groups">
            <h3>Pagina's</h3>
            <div class="inside">
                <?php foreach( $version['question_groups'] as $question_group_key => $question_group ): ?>
                    <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>" class="kwps-question_group kwps-box ">
                        <h3 class="collapsables">
                            <span class="kwps-collapse dashicons dashicons-arrow-right"></span> 
                                Pagina <?php echo $question_group['_kwps_sort_order'] ?> 
                            <button class="kwps-remove-item button button-small">remove</button>
                        </h3>
                        <div class="inside">
                            <?php $question_group_field_index = 'question_groups[' . $question_group['_kwps_sort_order'] .']' ?>
                            <?php if( isset ($question_group['ID'] ) ): ?>
                                <input type="hidden" name="ID" value="<?php echo $question_group['ID'] ?>" class="kwps-question_group_input"/>
                            <?php endif;?>
                            <input type="hidden" name="post_status" value="<?php echo $question_group['post_status'] ?>" class="kwps-question_group_input"/>
                            <div class="titlediv">
                                <div class="titlewrap">
                                    <input 
                                        type="text" 
                                        name="post_title" 
                                        value="<?php echo $question_group['post_title'] ?>" 
                                        class="kwps-post-title kwps-question_group_input <?php if( isset( $question_group['errors']['post_title'] ) ) echo 'kwps_error'; ?>" 
                                    />
                                </div>
                            </div>
                            <textarea style="display: none" name="post_content" class="kwps-question_group_input"><?php echo (isset($question_group['post_content']))? $question_group['post_content'] : "Page " . ($question_group_key+1) ?></textarea>
                            <div class="kwps-content<?php if( isset( $question_group['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                                <div style="display: none" class="kwps-content-editor">
                                    <?php wp_editor( (isset($question_group['post_content']))? $question_group['post_content'] : "Page " . ($question_group_key+1), 'question_group_' . $question_group_key, array('teeny' => true ) ); ?>
                                    <button class="kwps-content-editor-save">Save</button>
                                </div>
                                <div class="kwps-content-view">
                                    <div class="kwps-content-view-content">
                                        <?php echo (isset($question_group['post_content']))? $question_group['post_content'] : "Page " . ($question_group_key+1) ?>
                                    </div><a class="kwps-content-edit button">Edit</a>
                                </div>
                            </div>

                            <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>-questions" class="kwps kwps-multi kwps-questions kwps-box">
                                <h3>Vragen</h3>
                                <div class="inside">
                                    <?php foreach( $question_group['questions'] as $question_key => $question ) : ?>
                                        <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>-question-<?php echo $question['_kwps_sort_order'] ?>" class="kwps kwps-multi kwps-question kwps-box <?php if( isset( $question['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                                            <h3 class="collapsables">
                                                <span class="kwps-collapse dashicons dashicons-arrow-right"></span> Vraag 
                                                <span><?php echo $question['_kwps_sort_order'] ?></span> 
                                                <button class="kwps-remove-item button button-small">remove</button>
                                            </h3>
                                            <div class="inside">
                                                <?php if( isset ($question['ID'] ) ): ?>
                                                    <input type="hidden"
                                                           name="ID"
                                                           value="<?php echo $question['ID'] ?>" 
                                                           class="kwps-question_input"
                                                    />
                                                <?php endif;?>
                                                <input 
                                                    type="hidden" 
                                                    name="post_status" 
                                                    value="<?php echo $question['post_status'];?>" class="kwps-question_input" 
                                                />
                                                <textarea 
                                                    style="display: none" 
                                                    name="post_content" 
                                                    class="kwps-question_input">
                                                    <?php echo (isset($question['post_content']))? $question['post_content'] : "Question " . ($question_key+1) ?>
                                                </textarea>
                                                <div class="kwps-content">
                                                    <div style="display: none" class="kwps-content-editor">
                                                        <?php wp_editor( (isset($question['post_content']))? $question['post_content'] : "Question " . ($question_key+1), 'question_' . $question_group_key . '_' . $question_key, array('teeny' => true ) ); ?>
                                                        <button class="kwps-content-editor-save">Save</button>
                                                    </div>
                                                    <div class="kwps-content-view">
                                                        <div class="kwps-content-view-content">
                                                            <?php echo (isset($question['post_content']))? $question['post_content'] : "Question " . ($question_key+1) ?>
                                                        </div><a class="kwps-content-edit button">Edit</a>
                                                    </div>
                                                </div>

                                                <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>-question-<?php echo $question['_kwps_sort_order'] ?>-answer-options" class="kwps kwps-multi kwps-box kwps-answer_options">
                                                    <h3>Antwoorden</h3>
                                                    <div class="inside">
                                                        <?php foreach( $question['answer_options'] as $answer_option_key => $answer_option ): ?>
                                                            <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>-question-<?php echo $question['_kwps_sort_order'] ?>-answer_option-<?php echo $answer_option['_kwps_sort_order'] ?>" class="kwps-answer_option kwps-box">
                                                                <h3 class="collapsables">
                                                                    <span class="kwps-collapse dashicons dashicons-arrow-right"></span> Antwoord 
                                                                    <span><?php echo $answer_option['_kwps_sort_order'] ;?></span> 
                                                                    <button class="kwps-remove-item button button-small">remove</button>
                                                                </h3>
                                                                <div class="inside">
                                                                    <?php if( isset ($answer_option['ID'] ) ): ?>
                                                                        <input 
                                                                            type="hidden" 
                                                                            name="ID" 
                                                                            value="<?php echo $answer_option['ID'] ?>"
                                                                            class="kwps-answer_input" 
                                                                        />
                                                                    <?php endif;?>
                                                                    <input type="hidden" name="post_status" value="<?php echo $answer_option['post_status']; ?>" class="kwps-answer_input"/>
                                                                    <textarea style="display: none" name="post_content" class="kwps-answer_input"><?php echo (isset($answer_option['post_content']))? $answer_option['post_content'] : "Answer " . ($answer_option_key+1) ?></textarea>

                                                                    <div class="kwps-content<?php if( isset( $answer_option['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                                                                        <div style="display: none" class="kwps-content-editor">
                                                                            <?php wp_editor( (isset($answer_option['post_content']))? $answer_option['post_content'] : "Answer " . ($answer_option_key+1), 'answer_option_' . $question_group_key . '_' . $question_key . '_' . $answer_option_key, array('teeny' => true ) ); ?>
                                                                            <button class="kwps-content-editor-save">Save</button>
                                                                        </div>
                                                                        <div class="kwps-content-view">
                                                                            <div class="kwps-content-view-content">
                                                                                <?php echo (isset($answer_option['post_content']))? $answer_option['post_content'] : "Answer " . ($answer_option_key+1) ?>
                                                                            </div><a class="kwps-content-edit button">Edit</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                        <button class="kwps-create-item button" data-kwps-max="answer_options_per_question">
                                                            + AO
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <button class="kwps-create-item button" data-kwps-max="questions_per_question_group">
                                        + Q
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="kwps-create-item button" data-kwps-max="question_groups">
                + QG
            </button>
            </div>
        <div class="kwps kwps-single" id="kwps-outro">
            <h3>Outro result</h3>
            <div class="inside">
                <a class="button" id="kwps-add-result-button">Add result</a>
                <?php $outro = $version['outro']; ?>
                <input type="hidden" name="ID" value="<?php if( isset( $outro['ID'] ) ) echo $outro['ID'];?>">
                <input type="hidden" name="post_status" value="<?php if( isset( $outro['post_status'] ) ) echo $outro['post_status'];?>">
                <textarea style="display: none" name="post_content" ><?php echo (isset($outro['post_content']))? $outro['post_content'] : "Outro" ?></textarea>
                <div class="kwps-content<?php if( isset( $outro['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                    <div style="display: none" class="kwps-content-editor">
                        <?php wp_editor( (isset($outro['post_content']))? $outro['post_content'] : "Outro", 'outro', array('teeny' => true ) ); ?>
                        <button class="kwps-content-editor-save">Save</button>
                    </div>
                    <div class="kwps-content-view">
                        <div class="kwps-content-view-content">
                            <?php echo (isset($outro['post_content']))? $outro['post_content'] : "Outro" ?>
                        </div>
                        <a class="kwps-content-edit button">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        <button id="version-save" type="submit" class="button button-primary">Wijzigingen opslaan</button>
    </form>
</div>
