<div class="wrap" id="kwps_test">

</div> <!-- .wrap -->

<script id="cell_template" type="text/x-handlebars-template">
    <td>Dit is er eentje</td>
</script>

<script id="version_template" type="text/x-handlebars-template">
    <div id="icon-tests" class="icon32"><br/></div>
    <h2><?php echo get_admin_page_title() ?></h2>


    <table class="form-table">
        <tbody>
        <tr>
            <th>
                <label for="input-text">Text input</label>
            </th>
            <td>
                <input type="text" name="post_title" id="post_title" value="{{post_title}}" placeholder="<?php _e('New Test') ?>"/><br/>
            </td>
        </tr>
        </tbody>
    </table>
    <div id="tabs">
        <ul>
            <li><a href="#tabs-add"><?php _e( 'Edit content', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-results"><?php _e( 'Manage results', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-entries"><?php _e( 'Manage entries', 'klasse-wp-poll-survey' ) ?></a></li>
        </ul>
        <div id="tabs-add">
            <div>
                <div>
                    <button id="add-question"><?php _e('Add Question', 'klasse-wp-poll-survey') ?></button>
                    <button id="add-version"><?php _e('Add Version', 'klasse-wp-poll-survey') ?></button>
                </div>
                <div>
                    <table border="1px" id="matrix">
                        <tr>
                            <th class="no-delete">&nbsp;</th>
                            <td>&nbsp;</td>
                            {{#each versions}}
                            <td>
                                <div>{{post_title}}</div>
                                <div class="actions" style="display: none">edit | preview</div>
                            </td>
                            {{/each}}
                        </tr>
                        <tr>
                            <th class="no-delete"><?php _e('Intro', 'klasse-wp-poll-survey') ?></th>
                            <td id="_kwps_intro">
                                <div>
                                    {{_kwps_intro}}
                                </div>
                                <div class="actions" style="display: none">edit | preview</div>
                            </td>
                            {{#each versions}}
                            <td id="_kwps_intro_{{ID}}">
                                <div>{{_kwps_intro}}</div>
                                <div class="actions" style="display: none">edit | preview</div>
                            </td>
                            {{/each}}
                        </tr>
                        <tr>
                            <th class="no-delete toggle-details">Vraag 1</th>
                            <td id="_kwps_question">
                                <div>
                                    {{_kwps_question}}
                                </div>
                                <div class="actions" style="display: none">edit | preview</div>
                            </td>
                            {{#each versions}}
                            <td id="_kwps_question_{{ID}}">
                                <div>
                                    {{_kwps_question}}
                                </div>
                                <div class="actions" style="display: none">edit | preview</div>
                            </td>
                            {{/each}}
                        </tr>
                        <tr>
                            <th class="no-delete"><?php _e('Outro', 'klasse-wp-poll-survey') ?></th>
                            <td id="_kwps_outro">
                                <div>
                                    {{_kwps_outro}}
                                </div>
                                <div class="actions" style="display: none">edit | preview</div>
                            </td>
                            {{#each versions}}
                            <td id="_kwps_outro_{{ID}}">
                                <div>
                                    {{_kwps_outro}}
                                </div>
                                <div class="actions" style="display: none">edit | preview</div>
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