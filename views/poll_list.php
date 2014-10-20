<div class="wrap">

    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e(get_admin_page_title()) ?> <a href="?page=klasse-wp-poll-survey_edit" class="add-new-h2"><?php _e('New test') ?></a></h2>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="kwps-filter" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php $poll_list->display() ?>
    </form>

</div> <!-- .wrap -->
