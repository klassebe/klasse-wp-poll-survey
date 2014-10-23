<?php
    $is_existing_version = isset( $_REQUEST['id'] );
//var_dump($version_id);
?>


<div class="wrap">
    <h2>Versie</h2>
    <?php if( $is_existing_version ) :?>
        <?php $version = \kwps_classes\Version::get_as_array( $_REQUEST['id'] );?>
        <form action="?page=<?php echo $_REQUEST['page']; ?>&id=<?php echo $_REQUEST['id'];?>&action=edit_version" method="post" >
            <input type="text" name="post_title" id="kwps-post-title" value="<?php echo $version['post_title'] ?>"/>
            <input type="hidden" name="ID" value="<?php echo $_REQUEST['id'] ?>" />
                <div id="kwps-question-groups">
                    <?php $question_groups = \kwps_classes\Question_Group::get_all_by_post_parent( $_REQUEST['id'] ) ?>
                    <?php foreach( $question_groups as $question_group ): ?>
                        <div id="kwps-question-group-<?php echo $question_group['_kwps_sort_order'] ?>">
                            <h3>Pagina <?php echo $question_group['_kwps_sort_order'] ?></h3>
                            <?php $question_group_field_index = 'question_groups[' . $question_group['_kwps_sort_order'] .']' ?>
                            <input type="hidden"
                                   name="<?php echo $question_group_field_index; ?>[ID]"
                                   value="<?php echo $question_group['ID'] ?>" />
                            <input type="hidden"
                                   name="<?php echo $question_group_field_index; ?>[_kwps_sort_order]"
                                   value="<?php echo $question_group['_kwps_sort_order'] ?>" />
                            <input type="text"
                                   name="<?php echo $question_group_field_index; ?>[post_title]"
                                   value="<?php echo $question_group['post_title'] ?>" />
                            <input type="text"
                                   name="<?php echo $question_group_field_index; ?>[post_content]"
                                   value="<?php echo $question_group['post_content'] ?>" />

                            <div id="kwps-question-group-questions">
                                <?php $questions = \kwps_classes\Question::get_all_by_post_parent( $question_group['ID'] ); ?>
                                <?php foreach( $questions as $question ) : ?>
                                    <div id="kwps-question-group-question-<?php echo $question['_kwps_sort_order'] ?>">
                                        <h3>Vraag <?php echo $question['_kwps_sort_order'] ?></h3>
                                        <?php
                                            $question_field_index =
                                                $question_group_field_index . '[questions][' .
                                                $question['_kwps_sort_order'] . ']'
                                        ?>
                                        <input type="hidden"
                                               name="<?php echo $question_field_index; ?>[ID]"
                                               value="<?php echo $question['ID'] ?>" />
                                        <input
                                            type="hidden"
                                            name="<?php echo $question_field_index;?>[_kwps_sort_order]"
                                            value="<?php echo $question['_kwps_sort_order'];?>"
                                        />
                                        <input
                                            type="text"
                                            name="<?php echo $question_field_index;?>[post_content]"
                                            value="<?php echo $question['post_content'];?>"
                                        />
                                    <div id="kwps-question-group-question-answer-options">
                                        <?php $answer_options =
                                            \kwps_classes\Answer_Option::get_all_by_post_parent($question['ID'])
                                        ?>
                                        <?php foreach( $answer_options as $answer_option ): ?>
                                            <div id="kwps-question-group-question-answer-option-<?php echo $answer_option['_kwps_sort_order'] ?>">
                                                <h3>Antwoord <?php echo $answer_option['_kwps_sort_order'] ;?></h3>
                                                <?php
                                                    $answer_option_field_index = $question_field_index .
                                                        '[answer_options][' . $answer_option['_kwps_sort_order'] . ']';
                                                ?>
                                                <input type="hidden"
                                                       name="<?php echo $answer_option_field_index; ?>[ID]"
                                                       value="<?php echo $answer_option['ID'] ?>" />
                                                <input
                                                    type="hidden"
                                                    name="<?php echo $answer_option_field_index;?>[_kwps_sort_order]"
                                                    value="<?php echo $answer_option['_kwps_sort_order']; ?>"
                                                />
                                                <input
                                                    type="text"
                                                    name="<?php echo $answer_option_field_index;?>[post_content]"
                                                    value="<?php echo $answer_option['post_content']; ?>"
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
            <button type="submit">Wijzigingen opslaan</button>
        </form>
    <?php else:?>
        <form action="?page=<?php echo $_REQUEST['page']; ?>&action=edit_version&noheader=true" method="post">
            <input type="hidden" name="post_parent" id="kwps-post-parent" value="<?php echo $_REQUEST['post_parent'];?>" />
            <input type="text" name="post_title" id="kwps-post-title" placeholder="Versie naam/titel"/>
            <input type="text" name="intro" placeholder="Intro"/>
            <div id="kwps-question-groups" class="kwps-question-groups">
                <div id="kwps-question-group-1" class="kwps-question-group">
                    <h3>Pagina 1</h3>
                    <input type="hidden" name="question_groups[1][_kwps_sort_order]" value="1" />
                    <input type="text" name="question_groups[1][post_title]" placeholder="Pagina titel" />
                    <input type="text" name="question_groups[1][post_content]" placeholder="Pagina inhoud" />

                    <div id="kwps-question-group-questions" class="kwps-questions">
                        <div id="kwps-question-group-1-question-1" class="kwps-question">
                            <h3>Vraag 1</h3>

                            <input type="hidden" name="question_groups[1][questions][1][_kwps_sort_order]" value="1" />
                            <input type="text" name="question_groups[1][questions][1][post_content]" placeholder="Vraag inhoud" />

                            <div id="kwps-question-group-1-question-1-answer-options" class="kwps-answer-option">
                                <div id="kwps-question-group-1-question-1-answer-option-1" class="kwps-answer-option">
                                    <h3>Antwoord 1</h3>
                                    <input
                                        type="hidden"
                                        name="question_groups[1][questions][1][answer_options][1][_kwps_sort_order]"
                                        value="1"
                                    />
                                    <input
                                        type="text"
                                        name="question_groups[1][questions][1][answer_options][1][post_content]"
                                        placeholder="Antwoord-optie"
                                    />
                                </div>
                                <div id="kwps-question-group-1-question-1-answer-option-2">
                                    <h3>Antwoord 2</h3>
                                    <input
                                        type="hidden"
                                        name="question_groups[1][questions][1][answer_options][2][_kwps_sort_order]"
                                        value="2"
                                        />
                                    <input
                                        type="text"
                                        name="question_groups[1][questions][1][answer_options][2][post_content]"
                                        placeholder="Antwoord-optie"
                                        />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="text" name="outro" placeholder="Outro"/>
            <button type="submit">Wijzigingen opslaan</button>
        </form>
    <?php endif;?>
</div>
