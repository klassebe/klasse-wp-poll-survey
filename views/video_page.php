<h2><?php echo __( 'Video options', 'klasse-wp-poll-survey' ); ?></h2>
<p>
    <?php echo __( 'Enter a video URL in the input field and press the \'Insert into Post\' button to add this to the editor',
        'klasse-wp-poll-survey' );?>
</p>
<div id="video"></div>
<div class="clearfix">
		<label for="video-url"><?php echo __( 'Video url', 'klasse-wp-poll-survey' ); ?>
			<input type="text" id="video-url" width="150px" placeholder="http://www.youtube.com/...">
		</label>
		<label for="video-width"><?php echo __( 'Video width', 'klasse-wp-poll-survey' ); ?>
			<input type="number" id="video-width" val="560">
		</label>
		<label for="video-height"><?php echo __( 'Video height', 'klasse-wp-poll-survey' ); ?>
			<input type="number" id="video-height" val="315">
		</label>
			<br>
    <button id="add-video-to-editor" class="button"><?php echo __( 'Insert into Post' ); ?></button>
</div>