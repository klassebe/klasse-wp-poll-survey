<div class="wrap" id="kwps_test">
	<?php echo '<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>'; ?>
</div> <!-- .wrap -->

<script id="chooseTestModus_template" type="text/x-handlebars-template">
	<form id="create-new-test">
		<label>Test name</label>
		<input name="post_title">
		<ul>
			{{#each kwpsTestModi}}
			<li>
				<input type="radio" value="{{ID}}" name="post_parent" id="kwpsTestModi_{{ID}}"><label
					for="kwpsTestModi_{{ID}}">{{post_title}}</label>
			</li>
			{{/each}}
		</ul>
		<div>
			<button type="submit">Create</button>
		</div>
	</form>
</script>

<script id="edit_template" type="text/x-handlebars-template">
<?php echo '<link type="text/css" rel="stylesheet" href="'. plugins_url( '../css/editor.css', __FILE__ ) .'">'; ?>
	<h2>{{label}}</h2>
	<div>
    <button id="add-media-button"><span class="add-media-icon"></span><?php _e( 'Add Media', 'klasse-wp-poll-survey' ) ?></button>
		<form id="update-model">
            <div id="editor-tiny">   
			 <textarea name='text' rows="20">{{text}}</textarea>
            </div>
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
	<input type="text" name="post_title" id="post_title" value="{{title}}" placeholder="<?php _e( 'New Test' ) ?>"/>
</div>

<div id="tabs">
<ul>
	<li><a href="#tabs-add"><?php _e( 'Edit content', 'klasse-wp-poll-survey' ) ?></a></li>
	<li><a href="#tabs-results"><?php _e( 'Manage results', 'klasse-wp-poll-survey' ) ?></a></li>
	<li><a href="#tabs-entries"><?php _e( 'Manage entries', 'klasse-wp-poll-survey' ) ?></a></li>
	<li><a href="#tabs-settings"><?php _e( 'Manage settings', 'klasse-wp-poll-survey' ) ?></a></li>
</ul>
<div id="tabs-add">
	<div>
		<div>
			<table id="matrix" class="wp-list-table widefat fixed">
				<tr>
					<th class="no-delete column-action">&nbsp;</th>
					{{#each versions}}
					<th class=" column-title">
						<div class="column-tab">
							<div>{{post_title}}</div>
							<div class="actions">
                                <a class="delete-version" data-version-id="{{ID}}">delete</a>
                            </div>
						</div>
					</th>
					{{/each}}
					<td class="column-title" style="width:85px;">
						<div class="column-tab">
							<button class="add button" data-post-type="kwps_version" data-sort-order="{{@index}}">
								<span data-code="f132" class="dashicons dashicons-plus"></span>
							</button>
						</div>
					</td>
				</tr>
				<tr class="title">
					<th class="no-delete row-title" colspan="{{getColumnCount versions}}">
						<?php _e( 'Intro', 'klasse-wp-poll-survey' ) ?>
						{{#unless intro}}
						<button class="button add" data-post-type="kwps_intro">
							<span data-code="f132" class="dashicons dashicons-plus"></span>
						</button>
						{{/unless}}
					</th>
				</tr>
				{{#if intro}}
				<tr class="post-1 type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0">

					{{#each versions}}
					<td class="column-action">
						<div class="action">
							<a class="">
                                                <span class="del" data-type="unique" data-post-type="kwps_intro">
                                                    <span data-code="f182" class="dashicons dashicons-trash"></span>
                                                </span>
							</a>
						</div>
					</td>
					<td id="_kwps_intro_{{ID}}" class="post-title page-title column-title">
						<strong>
							<a class="row-title" href="#edit/{{kwpsIntro.ID}}"
							   title="Edit “{{subStringStripper kwpsIntro.post_content 100}}”">{{subStringStripper
								kwpsIntro.post_content 100}}</a>
						</strong>

						<div class="actions" style="display: none"><a href="#edit/{{kwpsIntro.ID}}">edit</a></div>
					</td>
					{{/each}}
				</tr>
				{{/if}}

				{{#each questionGroups}}
                <tr class="title">

				{{#each this}}
					<th class="no-delete row-title" colspan="{{getColumnCount ../../versions}}">
						{{#if ../open}}
						<span data-code="f140" class="dashicons dashicons-arrow-down toggle-details"
						      data-type="questionGroup" data-question-row="{{_kwps_sort_order}}"></span>
						{{else}}
						<span data-code="f139" class="dashicons dashicons-arrow-right toggle-details"
						      data-type="questionGroup" data-question-row="{{_kwps_sort_order}}"></span>
						{{/if}}
					    <input type="text" data-id="{{ID}}" value="{{post_title}}" name="post_title" class="update-post-title">
						<div>
							<strong>
								<a class="row-title" href="#edit/{{ID}}"
								   title="Edit “{{{subStringStripper post_content 30}}}”">{{{subStringStripper post_content
									100}}}</a>
							</strong>
						</div>
					</th>
				{{/each}}
                </tr>

				{{#if open}}


				{{#each ../../questions}}
				<tr>
					<td class="delete column-action {{#if open}} extra {{/if}}">
						{{#if open}}
						<span data-code="f140" class="dashicons dashicons-arrow-down toggle-details"
						      data-type="question" data-question-row="{{@index}}"></span>
						{{else}}
						<span data-code="f139" class="dashicons dashicons-arrow-right toggle-details"
						      data-type="question" data-question-row="{{@index}}"></span>
						{{/if}}
						<div class="action">
							<a class="delete-question">
                                            <span class="del" data-type="row" data-sort-order="{{@index}}" data-post-type="kwps_question">
                                                <span data-code="f182" class="dashicons dashicons-trash"></span>
                                            </span>
							</a>
						</div>
						<div class="move">
							{{{sorter @index ../../../questions}}}
						</div>
					</td>
					{{#each this}}
					<td id="_kwps_question_{{ID}}" class="post-title page-title column-title">
						<strong>
							<a class="row-title" href="#edit/{{ID}}"
							   title="Edit “{{subStringStripper post_content 100}}”">{{subStringStripper post_content
								100}}</a>
						</strong>

						<div class="actions" style="display: none"><a href="#edit/{{ID}}">edit</a></div>
					</td>
					{{/each}}
				</tr>
				{{#if open}}
				<tr class="title">
					<th class="no-delete answers row-title" colspan="{{getColumnCount ../../../../versions}}">
						<?php _e( 'Answers', 'klasse-wp-poll-survey' ) ?>
						<button class="button add" data-post-type="kwps_answer_option" data-sort-order="{{@index}}">
							<span data-code="f132"
							      class="dashicons dashicons-plus"></span> <?php _e( 'Add', 'klasse-wp-poll-survey' ) ?>
						</button>
					</th>
				</tr>
				{{#each ../../../../answers}}
				<tr class="{{lastItem " bottomborder
				" @index ../../../../../answers}} answer-row">
				<td class="delete column-action column-answer">
					<div class="action">
						<a class="delete-answer-option">
                                                    <span class="del" data-post-type="kwps_answer_option"
                                                          data-kwps-sort-order="{{@index}}">
                                                        <span data-code="f182" class="dashicons dashicons-trash"></span>
                                                    </span>
						</a>
					</div>
					<div class="move">
						{{{sorter @index}}}
					</div>
				</td>
				{{#each this}}
				<td id="_kwps_answer_option_{{ID}}" class="post-title page-title column-title">
					<strong>
						<a class="row-title" href="#edit/{{ID}}" title="Edit “{{subStringStripper post_content 100}}”">{{subStringStripper
							post_content 100}}</a>
					</strong>

					<div class="actions" style="display: none"><a href="#edit/{{ID}}">edit</a></div>
				</td>
				{{/each}}
				<td class="post-title page-title column-title">
				</td>
				</tr>
				{{/each}}




				{{/if}}
				{{/each}}

				{{#ifLength ../../questions ../../testmodus._kwps_max_questions_per_question_group}}
				<tr class="title">
					<th class="no-delete row-title" colspan="{{getColumnCount ../../../versions}}">
						<button class="button add" data-post-type="kwps_question" data-open-order="{{../../../open/questionGroup}}">
							<span data-code="f132" class="dashicons dashicons-plus"></span>
							<?php _e( 'Add Question', 'klasse-wp-poll-survey' ) ?>

						</button>
					</th>
				</tr>
				{{/ifLength}}


				{{/if}}
				{{/each}}

				{{#ifLength questionGroups testmodus._kwps_max_question_groups}}
				<tr class="title">
					<th class="no-delete row-title" colspan="{{getColumnCount versions}}">
						<button class="button add" data-post-type="kwps_question_group">
							<span data-code="f132" class="dashicons dashicons-plus"></span>
							<?php _e( 'Add Question Group', 'klasse-wp-poll-survey' ) ?>

						</button>
					</th>
				</tr>
				{{/ifLength}}

				<tr class="title">
					<th class="no-delete row-title" colspan="{{getColumnCount versions}}">
						<?php _e( 'Outro', 'klasse-wp-poll-survey' ) ?>
						{{#unless outro}}
						<button class="button add" data-post-type="kwps_outro">
							<span data-code="f132" class="dashicons dashicons-plus"></span>
						</button>
						{{/unless}}
					</th>
				</tr>
				{{#if outro}}
				<tr class="post-1 type-post status-publish format-standard hentry category-uncategorized iedit author-self level-0">
					{{#each versions}}
					<td class="column-action">
						<div class="action">
							<a class="delete-outro">
                                                <span class="del" data-type="unique" data-post-type="kwps_outro">
                                                    <span data-code="f182" class="dashicons dashicons-trash"></span>
                                                </span>
							</a>
						</div>
					</td>
					<td id="_kwps_Outro_{{ID}}" class="post-title page-title column-title">
						<strong>
							<a class="row-title" href="#edit/{{kwpsOutro.ID}}"
							   title="Edit “{{subStringStripper kwpsOutro.post_content 100}}”">{{subStringStripper
								kwpsOutro.post_content 100}}</a>
						</strong>

						<div class="actions" style="display: none"><a href="#edit/{{kwpsOutro.ID}}">edit</a></div>
					</td>
					{{/each}}
				</tr>
				{{/if}}
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
<div id="tabs-settings">
	<div>
		<h2>Limit entries</h2>

		<div>
			<label for="kwps_logged_in_user_limit"><?php _e( 'Logged in user' ) ?></label>
			<select id="kwps_logged_in_user_limit" name="_kwps_logged_in_user_limit" class="update-main">
				{{#each kwpsUniquenessTypes.logged_in}}
				<option value="{{function}}"
				{{selected this.function ../this.collection._kwps_logged_in_user_limit}} >{{label}}</option>
				{{/each}}
			</select>
		</div>
		<div>
			<label for="kwps_logged_out_user_limit"><?php _e( 'Logged out user' ) ?></label>
			<select id="kwps_logged_out_user_limit" name="_kwps_logged_out_user_limit" class="update-main">
				{{#each kwpsUniquenessTypes.logged_out}}
				<option value="{{function}}"
				{{selected this.function ../this.collection._kwps_logged_out_user_limit}} >{{label}}</option>
				{{/each}}
			</select>
		</div>
	</div>
</div>
</div>

</script>