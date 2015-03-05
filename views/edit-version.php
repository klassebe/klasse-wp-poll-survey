<?php
$form_action = '?page=' . $_REQUEST['page'] . '&section=edit_version';

if( isset( $_REQUEST['id'] ) ) {
    $test_modus = \kwps_classes\Version::get_test_modus( $_REQUEST['id'] );
    if( isset( $_REQUEST['update'] ) && 'true' == $_REQUEST['update'] ) {
        $version = $version_data;
    } else {
        $version = \kwps_classes\Version::get_with_all_children( $_REQUEST['id'] );
    }
    $form_action .= '&id=' . $_REQUEST['id'] . '&update=true';
} else {
    if( isset( $version_data ) ) {
        $test_modus = \kwps_classes\Test_Collection::get_test_modus( $version_data['post_parent'] );

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
        $test_modus = \kwps_classes\Test_Collection::get_test_modus( $_REQUEST['post_parent'] );

        if( $test_modus['_kwps_answer_options_require_value'] > 0 ) {
            $version['result_profiles'] = array(
                array(
                    'post_title' => '',
                    'post_status' => 'draft',
                    '_kwps_sort_order' => 0,
                    '_kwps_min_value' => 0,
                    '_kwps_max_value' => 10,
                ),
                array(
                    'post_title' => '',
                    'post_status' => 'draft',
                    '_kwps_sort_order' => 1,
                    '_kwps_min_value' => 11,
                    '_kwps_max_value' => 20,
                ),
            );

            $version['question_groups'][0]['questions'][0]['answer_options'][0]['_kwps_answer_option_value'] = 0;
            $version['question_groups'][0]['questions'][0]['answer_options'][1]['_kwps_answer_option_value'] = 0;
        }
    }
}
$test_collection = \kwps_classes\Test_Collection::get_as_array( $version['post_parent'], true );
$disable_form = ( isset( $test_collection['post_status'] ) && 'publish' == $test_collection['post_status'] );

$required_fields_version = \kwps_classes\Version::$required_fields;
$required_fields_intro = \kwps_classes\Intro::$required_fields;
$required_fields_outro = \kwps_classes\Outro::$required_fields;
$required_fields_result_profile = \kwps_classes\Result_Profile::$required_fields;
$required_fields_intro_result = \kwps_classes\Intro_Result::$required_fields;
$required_fields_question_group = \kwps_classes\Question_Group::$required_fields;
$required_fields_question = \kwps_classes\Question::$required_fields;
$required_fields_answer_option = \kwps_classes\Answer_Option::$required_fields;

$test_collection_url = get_admin_url() .'/admin.php?page=klasse-wp-poll-survey_edit&section=edit_test_collection&id=' . $version['post_parent'];
?>
<script language="JavaScript">
    var testModus = <?php echo json_encode($test_modus = \kwps_classes\Test_Collection::get_test_modus( $version['post_parent'] ) ) ?>;
    var versionData = <?php echo json_encode($version); ?>;
</script>
<div class="wrap">
    <a href="<?php echo $test_collection_url ?>">Terug</a>
    <h2>Versie <span class="kwps-gray">(<?php if( isset( $version['ID'] ) ) { echo $version['ID']; } else {echo 'Nieuw';}  ?>)</span></h2>
    <?php if( isset( $test_modus_errors ) && sizeof( $test_modus_errors ) > 0 ):?>
        <div id="test-modus-errors" class="error form-invalid below-h2">
        <?php foreach( $test_modus_errors as $rule => $message ) :?>
            <p class=""><?php echo $message ?></p>
        <?php endforeach; ?>
        </div>
    <?php endif;?>
    <form id="edit-version" action="<?php echo $form_action ?>" method="post" class="kwps-form">
        <?php if( in_array( 'post_title', $required_fields_version ) ) echo '<span class="kwps-required">*</span>' ?>
        <div class="kwps kwps-single" id="kwps-version">
            <?php if( isset( $version['ID'] ) ):?>
                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="ID" value="<?php echo $version['ID'] ?>" class="kwps-single_input"/>
            <?php endif;?>
            <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="_kwps_sort_order" value="<?php echo $version['_kwps_sort_order']; ?>" class="kwps-single_input">
            <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $version['post_parent']; ?>" class="kwps-single_input">
            <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_status" value="<?php echo $version['post_status']; ?>" class="kwps-single_input">
            <div class="titlediv ">
                <div class="titlewrap">
                    <label class="screen-reader-text" id="title-prompt-text" for="kwps-post-title">Enter title here</label>
                    <input <?php if( $disable_form ) echo 'disabled';?> 
                        type="text"
                        name="post_title"
                        id="kwps-post-title"
                        size="30"
                        spellcheck="true" 
                        autocomplete="off"
                        value="<?php echo $version['post_title'] ?>"
                        class="<?php if( isset( $version['errors']['post_title'] ) ) echo 'kwps_error'; ?>  kwps-post-title kwps-single_input "
                    />
                </div>
            </div>
        </div>
        <div class="kwps kwps-single<?php if( isset( $intro['errors']['post_content'] ) )  echo ' kwps_error'; ?>" id="kwps-intro">
            <h3>Intro <?php if( in_array( 'post_content', $required_fields_intro ) ) echo '<span class="kwps-required">*</span>' ?></h3>
            <div class="inside">
                <?php $intro = $version['intro'];?>
                <?php if( isset( $intro['ID'] ) ): ?>
                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="ID" value="<?php echo $intro['ID'];?>" class="kwps-single_input">
                <?php endif;?>
                <?php if( isset( $intro['post_parent'] ) ): ?>
                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $intro['post_parent'];?>" class="kwps-single_input">
                <?php endif;?>
                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_status" value="<?php if( isset( $intro['post_status'] ) ) echo $intro['post_status'];?>" class="kwps-single_input">

                <textarea style="display: none" name="post_content" class="kwps-single_input"><?php echo (isset($intro['post_content']))? $intro['post_content'] : "Intro" ?></textarea>
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
        <?php $intro_result = $version['intro_result'];?>
        <div class="kwps kwps-single<?php if( isset( $intro_result['errors']['post_content'] ) )  echo ' kwps_error'; ?>" id="kwps-intro_result">
            <h3>Intro result <?php if( in_array( 'post_content', $required_fields_intro_result ) ) echo '<span class="kwps-required">*</span>' ?></h3>
            <div class="inside">
                <?php if( isset( $intro_result['ID'] ) ): ?>
                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="ID" value="<?php echo $intro_result['ID'];?>" class="kwps-single_input">
                <?php endif;?>
                <?php if( isset( $intro_result['post_parent'] ) ): ?>
                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $intro_result['post_parent'];?>" class="kwps-single_input">
                <?php endif;?>
                <a class="button kwps-add-result-button intro-result-button">Add result</a>
                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_status" value="<?php if( isset( $intro_result['post_status'] ) ) echo $intro_result['post_status'];?>" class="kwps-single_input">
                <textarea style="display: none" name="post_content" class="kwps-single_input"><?php echo (isset($intro_result['post_content']))? $intro_result['post_content'] : "Intro Result" ?></textarea>

                <div class="kwps-content<?php if( isset( $intro_result['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                    <div style="display: none" class="kwps-content-editor">
                        <?php wp_editor( (isset($intro_result['post_content']))? $intro_result['post_content'] : "Intro Result", 'post_content_intro_result', array('teeny' => true ) ); ?>
                        <button class="kwps-content-editor-save button">Save</button>
                    </div>
                    <div class="kwps-content-view">
                        <div class="kwps-content-view-content">
                            <?php echo (isset($intro_result['post_content']))? $intro_result['post_content'] : "Intro Result" ?>
                        </div><a class="kwps-content-edit button">Edit</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="kwps-question_groups" class="kwps kwps-multi kwps-question_groups">
            <h3>Pagina's</h3>
            <div class="inside">
                <?php foreach( $version['question_groups'] as $question_group_key => $question_group ): ?>
                    <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>" class="kwps-question_group kwps-box <?php if( isset( $question_group['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                        <h3 class="collapsables">
                            <span class="kwps-collapse dashicons dashicons-arrow-right"></span> 
                                Pagina <?php echo $question_group['_kwps_sort_order'] ?> 
                                <?php if( in_array( 'post_content', $required_fields_question_group ) ) echo '<span class="kwps-required">*</span>' ?> 
                            <button class="kwps-remove-item button button-small">remove</button>
                        </h3>
                        <div class="inside">
                            <?php $question_group_field_index = 'question_groups[' . $question_group['_kwps_sort_order'] .']' ?>
                            <?php if( isset ($question_group['ID'] ) ): ?>
                                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="ID" value="<?php echo $question_group['ID'] ?>" class="kwps-question_group_input"/>
                            <?php endif;?>
                            <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="_kwps_sort_order" value="<?php echo $question_group['_kwps_sort_order'] ?>" class="kwps-question_group_input">
                            <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_status" value="<?php echo $question_group['post_status'] ?>" class="kwps-question_group_input"/>
                            <?php if( isset( $question_group['post_parent'] ) ) : ?>
                                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $question_group['post_parent']; ?>" class="kwps-single_input">
                            <?php endif;?>
                            <div class="titlediv">
                                <div class="titlewrap">
                                    <input <?php if( $disable_form ) echo 'disabled';?>  
                                        type="text" 
                                        name="post_title" 
                                        value="<?php echo $question_group['post_title'] ?>" 
                                        class="kwps-post-title kwps-question_group_input <?php if( isset( $question_group['errors']['post_title'] ) ) echo 'kwps_error'; ?>" 
                                    />
                                </div>
                            </div>
                            <textarea style="display: none" name="post_content" class="kwps-question_group_input"><?php echo (isset($question_group['post_content']))? $question_group['post_content'] : "Page " . ($question_group_key+1) ?></textarea>
                            <div class="kwps-content">
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
                                                <?php if( in_array( 'post_content', $required_fields_question ) ) echo '<span class="kwps-required">*</span>' ?>
                                                <button class="kwps-remove-item button button-small">remove</button>
                                            </h3>
                                            <div class="inside">
                                                <?php if( isset ($question['ID'] ) ): ?>
                                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden"
                                                           name="ID"
                                                           value="<?php echo $question['ID'] ?>" 
                                                           class="kwps-question_input"
                                                    />
                                                <?php endif;?>
                                                <input <?php if( $disable_form ) echo 'disabled';?>  
                                                    type="hidden" 
                                                    name="post_status" 
                                                    value="<?php echo $question['post_status'];?>" class="kwps-question_input" 
                                                />
                                                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="_kwps_sort_order" value="<?php echo $question['_kwps_sort_order'] ?>" class="kwps-question_input">
                                                <?php if( isset( $question['post_parent'] ) ) : ?>
                                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $question['post_parent']; ?>" class="kwps-single_input">
                                                <?php endif; ?>
                                                <textarea style="display: none" name="post_content" class="kwps-question_input"><?php echo ( isset($question['post_content'] ) ) ? $question['post_content'] : "Question " . ($question_key+1) ?></textarea>
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
                                                            <div id="kwps-question_group-<?php echo $question_group['_kwps_sort_order'] ?>-question-<?php echo $question['_kwps_sort_order'] ?>-answer_option-<?php echo $answer_option['_kwps_sort_order'] ?>" class="kwps-answer_option kwps-box <?php if( isset( $answer_option['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                                                                <h3 class="collapsables">
                                                                    <span class="kwps-collapse dashicons dashicons-arrow-right"></span> Antwoord 
                                                                    <span><?php echo $answer_option['_kwps_sort_order'] ;?></span> 
                                                                    <?php if( in_array( 'post_content', $required_fields_answer_option ) ) echo '<span class="kwps-required">*</span>' ?>
                                                                    <button class="kwps-remove-item button button-small">remove</button>
                                                                </h3>
                                                                <div class="inside">
                                                                    <?php if( isset ($answer_option['ID'] ) ): ?>
                                                                        <input <?php if( $disable_form ) echo 'disabled';?>  
                                                                            type="hidden" 
                                                                            name="ID" 
                                                                            value="<?php echo $answer_option['ID'] ?>"
                                                                            class="kwps-answer_input "
                                                                        />
                                                                    <?php endif;?>
                                                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_status" value="<?php echo $answer_option['post_status']; ?>" class="kwps-answer_input"/>
                                                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="_kwps_sort_order" value="<?php echo $answer_option['_kwps_sort_order'] ?>" class="kwps-answer_input">
                                                                    <?php if( isset( $answer_option['post_parent'] ) ) : ?>
                                                                        <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $answer_option['post_parent']; ?>" class="kwps-single_input">
                                                                    <?php endif; ?>
                                                                    <?php if( $test_modus['_kwps_answer_options_require_value'] > 0 ): ?>
                                                                        <div>
                                                                            <input <?php if( $disable_form ) echo 'disabled';?>  type="text" name="_kwps_answer_option_value" value="<?php echo $answer_option['_kwps_answer_option_value'];?>" class="kwps-answer_input">
                                                                        </div>
                                                                    <?php endif;?>
                                                                    <textarea style="display: none" name="post_content" class="kwps-answer_input"><?php echo (isset($answer_option['post_content']))? $answer_option['post_content'] : "Answer " . ($answer_option_key+1) ?></textarea>

                                                                    <div class="kwps-content">
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
                                                            + Antwoord toevoegen
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <button class="kwps-create-item button" data-kwps-max="questions_per_question_group">
                                        + Vraag toevoegen
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                <?php endforeach; ?>
                <button class="kwps-create-item button" data-kwps-max="question_groups">
                    + Pagina toevoegen
                </button>
            </div>
        </div>
        <?php $outro = $version['outro']; ?>
        <div class="kwps kwps-single <?php if( isset( $outro['errors']['post_content'] ) )  echo ' kwps_error'; ?>" id="kwps-outro">
            <h3>Outro result <?php if( in_array( 'post_content', $required_fields_outro ) ) echo '<span class="kwps-required">*</span>' ?></h3>
            <div class="inside">
                <?php if( isset( $outro['errors']['post_content'] ) ):?>
                <div class="error form-invalid below-h2">
                        <p class=""><?php echo $outro['errors']['post_content'] ?></p>
                </div>
                <?php endif;?>
                <a class="button kwps-add-result-button outro-result-button">Add result</a>
                <?php if( isset( $outro['ID'] ) ): ?>
                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="ID" value="<?php echo $outro['ID'];?>" class="kwps-single_input">
                <?php endif;?>
                <?php if( isset( $outro['post_parent'] ) ): ?>
                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $outro['post_parent'];?>" class="kwps-single_input">
                <?php endif;?>
                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_status" value="<?php if( isset( $outro['post_status'] ) ) echo $outro['post_status'];?>" class="kwps-single_input">
                <textarea style="display: none" name="post_content" class="kwps-single_input"><?php echo (isset($outro['post_content']))? $outro['post_content'] : "Outro" ?></textarea>
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
        <?php if( isset( $version['result_profiles'] ) ): ?>
                <div class="kwps kwps-multi kwps-result_profiles" id="kwps-result_profiles">
                    <h3>Result profiles <?php if( in_array( 'post_content', $required_fields_result_profile ) ) echo '<span class="kwps-required">*</span>' ?></h3>
                    <div class="inside">
                        <?php foreach( $version['result_profiles'] as $result_profile_key => $result_profile ): ?>
                        <div id="kwps-result_profile-<?php echo $result_profile['_kwps_sort_order'] ?>" class="kwps-result_profile kwps-box <?php if( isset( $result_profile['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                            <h3 class="collapsables">
                                <span class="kwps-collapse dashicons dashicons-arrow-right"></span>
                                <span class="kwps-result_profile_head_title">
                                    Result profile<!--  <?php echo $result_profile['_kwps_min_value'] ;?> - <?php echo $result_profile['_kwps_max_value'] ;?> -->
                                </span> 
                                <button class="kwps-remove-item button button-small">remove</button>
                            </h3>
                            <div class="inside">
                                <?php if( isset( $result_profile['ID'] ) ): ?>
                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="ID" value="<?php echo $result_profile['ID'] ?>"  class="kwps-question_group_input">
                                <?php endif;?>
                                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_status" value="<?php echo $result_profile['post_status'] ?>"  class="kwps-question_group_input">
                                <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="_kwps_sort_order" value="<?php echo $result_profile['_kwps_sort_order'] ?>"  class="kwps-question_group_input">
                                <?php if( isset( $result_profile['post_parent'] ) ): ?>
                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="hidden" name="post_parent" value="<?php echo $result_profile['post_parent']; ?>" class="kwps-single_input">
                                <?php endif; ?>
                                <div class="titlediv">
                                    <div class="titlewrap">
                                        <input <?php if( $disable_form ) echo 'disabled';?> 
                                            type="text"
                                            name="post_title"
                                            value="<?php echo $result_profile['post_title'] ?>"
                                            class="kwps-post-title kwps-question_group_input <?php if( isset( $result_profile['errors']['post_title'] ) ) echo 'kwps_error'; ?>"
                                            />
                                    </div>
                                </div>
                                <div>
                                    <label>Score van</label>
                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="text" name="_kwps_min_value" value="<?php echo $result_profile['_kwps_min_value'] ?>" class="kwps-question_group_input">
                                    <label>tot</label>
                                    <input <?php if( $disable_form ) echo 'disabled';?>  type="text" name="_kwps_max_value" value="<?php echo $result_profile['_kwps_max_value'] ?>" class="kwps-question_group_input">
                                </div>
                                <textarea style="display: none" name="post_content" class="kwps-question_group_input"><?php echo (isset($result_profile['post_content']))? $result_profile['post_content'] : "" ?></textarea>
                                <div class="kwps-content">
                                    <div style="display: none" class="kwps-content-editor">
                                        <?php wp_editor( (isset($result_profile['post_content']))? $result_profile['post_content'] : "", 'result_profile_' . $result_profile_key, array('teeny' => true ) ); ?>
                                        <button class="kwps-content-editor-save">Save</button>
                                    </div>
                                    <div class="kwps-content-view">
                                        <div class="kwps-content-view-content">
                                            <?php echo (isset($result_profile['post_content']))? $result_profile['post_content'] : "" ?>
                                        </div><a class="kwps-content-edit button">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <button class="kwps-create-item button" data-kwps-max="result_profiles">
                            + profile toevoegen
                        </button>
                    </div>
                </div>
        <?php endif;?>
        <button id="version-save" type="submit" class="button button-primary">Wijzigingen opslaan</button>
    </form>
    <div class="kwps-nav-spacer"></div>
    <a href="<?php echo $test_collection_url ?>">Terug</a>

</div>
