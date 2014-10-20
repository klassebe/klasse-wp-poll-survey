<h2><?php echo __( 'Video options', 'klasse-wp-poll-survey' ); ?></h2>
<p>
    <?php echo __( 'Enter a YouTube URL with a given width and height and press the \'Insert into Post\' button to add the video to the editor.',
        'klasse-wp-poll-survey' );?>
</p>
<div id="video"></div>
<div class="clearfix">
		<label for="video-url"><?php echo __( 'Video url', 'klasse-wp-poll-survey' ); ?></label>
			<input type="text" id="video-url" size="50" placeholder="http://www.youtube.com/...">
		<br>
		<label for="video-width"><?php echo __( 'Video width (560)', 'klasse-wp-poll-survey' ); ?></label>
			<input type="number" id="video-width" value="560">
		<br>
		<label for="video-height"><?php echo __( 'Video height (315)', 'klasse-wp-poll-survey' ); ?></label>
			<input type="number" id="video-height" value="315">
			<br>
    <button id="add-video-to-editor" class="button"><?php echo __( 'Insert into Post' ); ?></button>
</div>