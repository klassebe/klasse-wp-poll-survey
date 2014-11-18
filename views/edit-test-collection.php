<?php
    $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'versions';
?>

<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&section=edit_test_collection&tab=versions" class="nav-tab <?php echo $active_tab == 'versions' ? 'nav-tab-active' : ''; ?>">Versions</a>
        <a href="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&section=edit_test_collection&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
    </h2>

    <?php if($active_tab == 'versions'): ?>

    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e('Manage versions', 'klasse-wp-poll-survey') ?> <a href="?page=klasse-wp-poll-survey_edit&section=edit_version&post_parent=<?php echo $_REQUEST['id']; ?>" class="add-new-h2"><?php _e('New version') ?></a></h2>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="kwps-filter" method="get" >
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <input type="hidden" name="section" value="<?php echo $_REQUEST['section'] ?>" />
        <input type="hidden" name="id" value="<?php echo $_REQUEST['id'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php

        $versions_list->display();
        ?>
    </form>
    <?php else:?>
        <?php $allowed_dropdown_values = \kwps_classes\Test_Collection::$allowed_dropdown_values; ?>
        <form>
            <label for="kwps_logged_in_user_limit">Aangemelde gebruikers</label>
            <select id="kwps_logged_in_user_limit">
            <?php foreach( $allowed_dropdown_values['_kwps_logged_in_user_limit'] as $value ): ?>
                <option name="_kwps_logged_in_user_limit"><?php echo $value; ?></option>
            <?php endforeach; ?>
            </select>

            <label for="kwps_logged_out_user_limit">Anonieme gebruikers</label>
            <select id="kwps_logged_out_user_limit">
                <?php foreach( $allowed_dropdown_values['_kwps_logged_out_user_limit'] as $value ): ?>
                    <option name="_kwps_logged_out_user_limit"><?php echo $value; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="kwps_show_grouping_form">Show grouping form</label>
            <input type="checkbox" name="_kwps_show_grouping_form" />
            <button type="submit">Opslaan</button>
        </form>
    <?php endif; ?>

</div> <!-- .wrap -->
