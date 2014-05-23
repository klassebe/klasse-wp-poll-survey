<?php 
/** Load WordPress Administration Bootstrap */
// require_once( '../../../../wp-admin/admin.php');

// if (!current_user_can('upload_files'))
// 	wp_die(__('You do not have permission to upload files.'));

// wp_enqueue_script('plupload-handlers');
// wp_enqueue_script('image-edit');
// wp_enqueue_script('set-post-thumbnail' );
// wp_enqueue_style('imgareaselect');
// wp_enqueue_script( 'media-gallery' );

// @header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
// // IDs should be integers
// $ID = isset($ID) ? (int) $ID : 0;
// $post_id = isset($post_id)? (int) $post_id : 0;

// // Require an ID for the edit screen
// if ( isset($action) && $action == 'edit' && !$ID )
// 	wp_die( __( 'Cheatin&#8217; uh?' ) );

// 	if ( ! empty( $_REQUEST['post_id'] ) && ! current_user_can( 'edit_post' , $_REQUEST['post_id'] ) )
// 		wp_die( __( 'Cheatin&#8217; uh?' ) );

// 	// upload type: image, video, file, ..?
// 	if ( isset($_GET['type']) ) {
// 		$type = strval($_GET['type']);
// 	} else {
// 		/**
// 		 * Filter the default media upload type in the legacy (pre-3.5.0) media popup.
// 		 *
// 		 * @since 2.5.0
// 		 *
// 		 * @param string $type The default media upload type. Possible values include
// 		 *                     'image', 'audio', 'video', 'file', etc. Default 'file'.
// 		 */
// 		$type = apply_filters( 'media_upload_default_type', 'file' );
// 	}

// 	// tab: gallery, library, or type-specific
// 	if ( isset($_GET['tab']) ) {
// 		$tab = strval($_GET['tab']);
// 	} else {
// 		*
// 		 * Filter the default tab in the legacy (pre-3.5.0) media popup.
// 		 *
// 		 * @since 2.5.0
// 		 *
// 		 * @param string $type The default media popup tab. Default 'type' (From Computer).
		 
// 		$tab = apply_filters( 'media_upload_default_tab', 'type' );
// 	}

// 	$body_id = 'media-upload';

// 	// let the action code decide how to handle the request
// 	if ( $tab == 'type' || $tab == 'type_url' || ! array_key_exists( $tab , media_upload_tabs() ) ) {
// 		/**
// 		 * Fires inside specific upload-type views in the legacy (pre-3.5.0)
// 		 * media popup based on the current tab.
// 		 *
// 		 * The dynamic portion of the hook name, $type, refers to the specific
// 		 * media upload type. Possible values include 'image', 'audio', 'video',
// 		 * 'file', etc.
// 		 *
// 		 * The hook only fires if the current $tab is 'type' (From Computer),
// 		 * 'type_url' (From URL), or, if the tab does not exist (i.e., has not
// 		 * been registered via the 'media_upload_tabs' filter.
// 		 *
// 		 * @since 2.5.0
// 		 */
// 		do_action( "media_upload_$type" );
// 	} else {
// 		/**
// 		 * Fires inside limited and specific upload-tab views in the legacy
// 		 * (pre-3.5.0) media popup.
// 		 *
// 		 * The dynamic portion of the hook name, $tab, refers to the specific
// 		 * media upload tab. Possible values include 'library' (Media Library),
// 		 * or any custom tab registered via the 'media_upload_tabs' filter.
// 		 *
// 		 * @since 2.5.0
// 		 */
// 		do_action( "media_upload_$tab" );
// 	}
?>
<style type="text/css">
	body {
		font-family: 'Open Sans', sans-serif;
	}
	.left {
		float: left;
	}
	.clearfix {
		clear: both;
	}
	textarea, input, select, button {
		font-family: inherit;
		font-size: 13px;
		font-weight: inherit;
	}
	#bar-chart,
	#pie-chart,
	#line-chart {

	}
</style>
<h2>Select a chart</h2>
<p>Choose a chart and press the insert into button to add this to the editor</p>
<div id="charts">
	<div id="bar-chart" class="media-item left">
		<label>
			<h4>Bar Chart</h4>
			<input type="radio" name="charts" value="bar_chart">
			<img class="thumbnail" src="images/bar_chart.png" alt height="128" width="128">
		</label>
	</div>
	<div id="pie-chart" class="media-item left">
		<label>
			<h4>Pie Chart</h4>
			<input type="radio" name="charts" value="pie_chart">
			<img class="thumbnail" src="images/bar_chart.png" alt height="128" width="128">
		</label>
	</div>
	<div id="line-chart" class="media-item left">
		<label>
			<h4>Line Chart</h4>
			<input type="radio" name="charts" value="line_chart">
			<img class="thumbnail" src="images/bar_chart.png" alt height="128" width="128">
		</label>
	</div>
</div>
<div class="clearfix">
	<button id="add-result-to-editor" class="button">Add Chart</button>
</div>

<script src="../../../../wp-includes/js/jquery/jquery.js"></script>
<script src="../../../../wp-includes/js/thickbox/thickbox.js"></script>
<script type="text/javascript">
	// var getInputs = document.getElementsByTagName('input');
	// for (var i = getInputs.length - 1; i >= 0; i--) {
	// 	getInputs[i].style.display = 'none';
	// }
jQuery(function ($) {
	$('input:radio').hide();
	$('input:radio').on('click', function () {
		    console.log('you clicked to add result to editor');
      $('iframe').contents().find('#tinymce').append('<div class="kwps-chart"></div>');
      tb_remove();
	});
});
</script>