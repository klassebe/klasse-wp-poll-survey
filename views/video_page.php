<h2><?php echo __( 'Video options', 'klasse-wp-poll-survey' ); ?></h2>
<p>
    <?php echo __( 'Enter a video URL in the input field and press the \'Insert into Post\' button to add this to the editor',
        'klasse-wp-poll-survey' );?>
</p>
<div id="video"></div>
<div class="clearfix">
		<label for="video-url"></label>
			<input type="text" id="video-url" placeholder="http://www.youtube.com/...">
    <button id="add-video-to-editor" class="button"><?php echo __( 'Insert into Post' ); ?></button>
</div>