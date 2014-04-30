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
</script>

<script id="question_template" type="text/x-handlebars-template">
    <div>
        {{question}}
    </div>
    <div>
        {{#each answers}}
            <div>{{post_content}}</div>
        {{/each}}
    </div>
</script>

<script id="version_template" type="text/x-handlebars-template">
    <div id="icon-tests" class="icon32"><br/></div>
    <h2><?php echo get_admin_page_title() ?></h2>

    <div class="test-input">
        <input type="text" name="post_title" id="post_title" value="{{title}}" placeholder="<?php _e('New Test') ?>"/>
    </div>

    <div id="tabs">
        <ul>
            <li><a href="#tabs-add"><?php _e( 'Edit content', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-results"><?php _e( 'Manage results', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-entries"><?php _e( 'Manage entries', 'klasse-wp-poll-survey' ) ?></a></li>
        </ul>
        <div id="tabs-add">
            <div>
                <div>
                    <table id="matrix" class="wp-list-table widefat fixed">
                        <tr>
                            <th class="no-delete column-action">&nbsp;</th>
                            {{#each versions}}
                                <td class=" column-title">
                                    {{#unless main}}
                                        <div>{{post_title}}</div>
                                        <div class="actions" style="display: none" data-kwps-id="{{ID}}">edit | <a class="delete-version">delete</a></div>
                                    {{else}}
                                        <div id="buttons">
                                            <button id="add-version" class="button"><?php _e('Add Version', 'klasse-wp-poll-survey') ?></button>
                                        </div>
                                    {{/unless}}
                                </td>
                            {{/each}}
                        </tr>
                        <tr class="title">
                            <th class="no-delete row-title" colspan="{{getColumnCount versions}}"><?php _e( 'Intro', 'klasse-wp-poll-survey' ) ?></th>
                        </tr>
                        <tr class="post-1 type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0">
                            <td class="column-action"></td>
                            {{#each versions}}
                            <td id="_kwps_intro_{{ID}}" class="post-title page-title column-title">
                                <strong>
                                    <a class="row-title" href="#edit/{{kwpsIntro.ID}}" title="Edit “{{subStringStripper kwpsIntro.post_content 100}}”">{{subStringStripper kwpsIntro.post_content 100}}</a>
                                </strong>
                                <div class="actions" style="display: none"><a href="#edit/{{kwpsIntro.ID}}">edit</a></div>
                            </td>
                            {{/each}}
                        </tr>
                        <tr class="title">
                            <th class="no-delete row-title"  colspan="{{getColumnCount versions}}"><?php _e( 'Questions', 'klasse-wp-poll-survey' ) ?> <button class="button add" id="add-question"><?php _e( 'Add', 'klasse-wp-poll-survey' ) ?></button></th>
                        </tr>
                        {{#each questions}}
                        <tr>
                            <td class="delete column-action {{#if open}} extra {{/if}}">
                                {{#if open}} 
                                    <span data-code="f343" class="dashicons dashicons-arrow-up-alt2 toggle-details" data-question-row ="{{@index}}"></span> 
                                {{else}} 
                                    <span data-code="f347" class="dashicons dashicons-arrow-down-alt2 toggle-details" data-question-row ="{{@index}}"></span> 
                                {{/if}}
                                <span class="del">Delete</span>
                                <div class="move">
                                    {{{sorter @index ../questions}}}
                                </div>
                            </td>
                            {{#each this}}
                            <td id="_kwps_question_{{ID}}" class="post-title page-title column-title">
                                <strong>
                                    <a class="row-title" href="#edit/{{ID}}" title="Edit “{{post_content}}”">{{post_content}}</a>
                                </strong>
                                <div class="actions" style="display: none"><a href="#edit/{{ID}}">edit</a></div>
                            </td>
                            {{/each}}
                        </tr>
                        {{#if open}}
                        <tr class="title">
                            <th class="no-delete answers row-title" colspan="{{getColumnCount ../../versions}}">
                                <?php _e( 'Answers', 'klasse-wp-poll-survey' ) ?> 
                                <button class="button add-answer"><?php _e( 'Add', 'klasse-wp-poll-survey' ) ?></button>
                                </th>
                        </tr>
                            {{#each ../../answers}}
                                <tr class="{{lastItem "bottomborder" @index ../../../answers}} answer-row">
                                    <td class="delete column-action column-answer">
                                        <span class="del">Delete</span>
                                        <div class="move">
                                            {{{sorter @index}}}
                                        </div>
                                    </td>
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
                            <td class="delete column-action">
                                <span class="del">Delete</span>
                            </td>
                            {{#each versions}}
                            <td id="kwpsOutro_{{ID}}">
                                <div>
                                    {{kwpsOutro.post_content}}
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