<div class="wrap" id="kwps_test">
<?php echo '<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>'; ?>
</div> <!-- .wrap -->

<script id="edit_template" type="text/x-handlebars-template">
<h2>{{label}}</h2>
<div>
    <form id="update-model">
        <textarea name='text' rows="20">{{text}}</textarea>
        <button id="update"><?php _e( 'Update', 'klasse-wp-poll-survey' ) ?></button>
    </form>
</div>
<?php
    echo '<script>jQuery(function($){
                        tinymce.init({
                            selector: "textarea"
                        });
                }); console.log("testje")</script>'; ?>
</script>

<script id="iframe_template" type="text/x-handlebars-template">
    <iframe src="<?php echo admin_url('/post-new.php?post_type=kwps_question'); ?>" width="100%" height="600px" scrolling="no"></iframe>
    <div id="load-data" width="100%" height="400px"></div>
    <textarea></textarea>
</script>

<script id="version_template" type="text/x-handlebars-template">
    <div id="icon-tests" class="icon32"><br/></div>
    <h2><?php echo get_admin_page_title() ?></h2>

    <div class="test-input">
        <input type="text" name="post_title" id="post_title" value="{{post_title}}" placeholder="<?php _e('New Test') ?>"/>
    </div>

    <div id="tabs">
        <ul>
            <li><a href="#tabs-add"><?php _e( 'Edit content', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-results"><?php _e( 'Manage results', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-entries"><?php _e( 'Manage entries', 'klasse-wp-poll-survey' ) ?></a></li>
        </ul>
        <div id="tabs-add">
            <div>
                <div id="buttons">
                    <button id="add-version"><?php _e('Add Version', 'klasse-wp-poll-survey') ?></button>
                </div>
                <div>
                    <table border="1px" id="matrix">
                        <tr>
                            <td>

                            </td>
                            <th class="no-delete">&nbsp;</th>
                            {{#each versions}}
                            <td>
                                <div>{{post_title}}</div>
                                <div class="actions" style="display: none" data-kwps-id="{{ID}}">edit | <a class="delete-version">delete</a></div>
                            </td>
                            {{/each}}
                        </tr>
                        <tr class="title">
                            <th class="no-delete" colspan="{{getColumnCount versions}}"><?php _e( 'Intro', 'klasse-wp-poll-survey' ) ?></th>
                        </tr>
                        <tr>
                            <td class="delete">
                                <span class="del">Delete</span>
                                <div class="move">
                                    <span class="up"></span>
                                    <span class="down"></span>
                                </div>
                            </td>
                            <td id="_kwps_intro">
                                <div>
                                    {{_kwps_intro}}
                                </div>
                                <div class="actions" style="display: none" data-kwps-attribute="_kwps_intro"><span class="edit">edit</span> | <span class="preview">preview</span></div>
                            </td>
                            {{#each versions}}
                            <td id="_kwps_intro_{{ID}}">
                                <div>{{_kwps_intro}}</div>
                                <div class="actions" style="display: none"><span class="edit">edit</span> | <span class="preview">preview</span></div>
                            </td>
                            {{/each}}
                        </tr>
                        <tr class="title">
                            <th class="no-delete"  colspan="{{getColumnCount versions}}"><?php _e( 'Questions', 'klasse-wp-poll-survey' ) ?> <button class="button add" id="add-question"><?php _e( 'Add', 'klasse-wp-poll-survey' ) ?></button></th>
                        </tr>
                        {{#each questions}}
                        <tr>
                            <td class="delete">
                                <span class="toggle-details">Toggle</span>
                                <span class="del">Delete</span>
                                <div class="move">
                                    <span class="up"></span>
                                    <span class="down"></span>
                                </div>
                            </td>
                            <td id="_kwps_question">
                                <div>
                                    {{_kwps_question}}
                                </div>
                                <div class="actions" style="display: none" data-kwps-attribute="_kwps_question"><span class="edit">edit</span> | <span class="preview">preview</span></div>
                            </td>
                            {{#each ../versions}}
                            <td id="_kwps_question_{{ID}}">
                                <div>
                                    {{_kwps_question}}
                                </div>
                                <div class="actions" style="display: none" data-kwps-attribute="_kwps_question"><span class="edit">edit</span> | <span class="preview">preview</span></div>
                            </td>
                            {{/each}}
                        </tr>
                        {{#if open}}
                        <tr class="title">
                            <th class="no-delete answers" colspan="{{getColumnCount versions}}"><?php _e( 'Answers', 'klasse-wp-poll-survey' ) ?> <button class="add-answer"><?php _e( 'Add', 'klasse-wp-poll-survey' ) ?></button></th>
                        </tr>
                            {{#each table}}
                                <tr>
                                    {{#each this}}
                                            <td class="answer">
                                                <div>
                                                    {{answer_option}}
                                                </div>
                                                <div class="actions" style="display: none">edit | <span class="preview">preview</span></div>
                                            </td>
                                    {{/each}}
                                </tr>
                            {{/each}}
                        {{/if}}
                        {{/each}}
                        <tr class="title">
                            <th class="no-delete" colspan="{{getColumnCount versions}}"><?php _e( 'Outro', 'klasse-wp-poll-survey' ) ?></th>
                        </tr>
                        <tr>
                            <td class="delete">
                                <span class="del">Delete</span>
                                <div class="move">
                                    <span class="up"></span>
                                    <span class="down"></span>
                                </div>
                            </td>
                            <td id="_kwps_outro">
                                <div>
                                    {{_kwps_outro}}
                                </div>
                                <div class="actions" style="display: none" data-kwps-attribute="_kwps_outro"><span class="edit">edit</span> | <span class="preview">preview</span></div>
                            </td>
                            {{#each versions}}
                            <td id="_kwps_outro_{{ID}}">
                                <div>
                                    {{_kwps_outro}}
                                </div>
                                <div class="actions" style="display: none">edit | <span class="preview">preview</span></div>
                            </td>
                            {{/each}}
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div id="tabs-results">
            <p>Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus id
                nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros molestie
                lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in ligula
                suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis. Curabitur
                ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat. Pellentesque
                convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod felis, eu ornare
                leo nisi vel felis. Mauris consectetur tortor et purus.</p>
        </div>
        <div id="tabs-entries">
            <p>EEEEE Morbi tincidunt, dui sit amet facilisis feugiat, odio metus gravida ante, ut pharetra massa metus
                id nunc. Duis scelerisque molestie turpis. Sed fringilla, massa eget luctus malesuada, metus eros
                molestie lectus, ut tempus eros massa ut dolor. Aenean aliquet fringilla sem. Suspendisse sed ligula in
                ligula suscipit aliquam. Praesent in eros vestibulum mi adipiscing adipiscing. Morbi facilisis.
                Curabitur ornare consequat nunc. Aenean vel metus. Ut posuere viverra nulla. Aliquam erat volutpat.
                Pellentesque convallis. Maecenas feugiat, tellus pellentesque pretium posuere, felis lorem euismod
                felis, eu ornare leo nisi vel felis. Mauris consectetur tortor et purus.</p>
        </div>
    </div>

</script>