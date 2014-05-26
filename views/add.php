<?php echo '<link type="text/css" rel="stylesheet" href="'. plugins_url( '../css/editor.css', __FILE__ ) .'">'; ?>
<?php echo '<script src="' . plugins_url( '../js/tinymce/tinymce.min.js', __FILE__ ) .'"></script>'; ?>
<script src="http://localhost:35729/livereload.js"></script>
<div class="wrap" id="kwps_test">
	
    <div id="icon-tests" class="icon32"><br/></div>
    <h2><?php echo get_admin_page_title() ?></h2>

    <div class="test-input">
        <input type="text" name="post_title" id="post_title" placeholder="<?php _e( 'New Test' ) ?>"/>
    </div>

    <div id="tabs">
        <ul>
            <li><a href="#tabs-add"><?php _e( 'Edit content', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-results"><?php _e( 'Manage results', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-entries"><?php _e( 'Manage entries', 'klasse-wp-poll-survey' ) ?></a></li>
            <li><a href="#tabs-settings"><?php _e( 'Manage settings', 'klasse-wp-poll-survey' ) ?></a></li>
        </ul>
        <div id="tabs-add">
        </div>
        <div id="tabs-results">

        </div>
        <div id="tabs-entries">
            
        </div>
        <div id="tabs-settings">
            
        </div>
    </div>



</div> <!-- .wrap -->