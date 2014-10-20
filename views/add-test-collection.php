<div class="wrap">

    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e(get_admin_page_title()) ?> <a href="?page=klasse-wp-poll-survey_addnew#new" class="add-new-h2"><?php _e('New test') ?></a></h2>

    <form id="kwps-test-collection" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <select id="kwps_test_modus">
            <option value="1">Poll</option>
        </select>
    </form>

</div> <!-- .wrap -->