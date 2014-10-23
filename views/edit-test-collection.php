<?php
    $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'versions';
?>

<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&tab=versions" class="nav-tab <?php echo $active_tab == 'versions' ? 'nav-tab-active' : ''; ?>">Versions</a>
        <a href="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
    </h2>

    <?php if($active_tab == 'versions'): ?>

    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e('Manage versions', 'klasse-wp-poll-survey') ?> <a href="?page=klasse-wp-poll-survey_edit&action=add_version&post_parent=<?php echo $_REQUEST['id']; ?>" class="add-new-h2"><?php _e('New version') ?></a></h2>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="kwps-filter" method="get" >
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php

        $versions_list->display();
        ?>
    </form>
    <?php else:?>
        <form>
            <button type="submit">Opslaan</button>
        </form>
        <div>Settings page</div>
    <?php endif; ?>

</div> <!-- .wrap -->
