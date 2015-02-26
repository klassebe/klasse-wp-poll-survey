<?php
    $active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'versions';
    $test_collection = \kwps_classes\Test_Collection::get_as_array( $_REQUEST['id'], true );
    $required_fields_coll_outro = \kwps_classes\Coll_Outro::$required_fields;
    $coll_outro = \kwps_classes\Coll_Outro::get_one_by_post_parent( $_REQUEST['id'] );
?>

<div class="wrap">
    <h2 class="nav-tab-wrapper">
        <a href="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&section=edit_test_collection&tab=versions" class="nav-tab <?php echo $active_tab == 'versions' ? 'nav-tab-active' : ''; ?>">Versions</a>
        <a href="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&section=edit_test_collection&tab=settings" class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>">Settings</a>
        <a href="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&section=edit_test_collection&tab=results" class="nav-tab <?php echo $active_tab == 'results' ? 'nav-tab-active' : ''; ?>">Results</a>
    </h2>

    <?php if($active_tab == 'versions'): ?>

    <div id="icon-users" class="icon32"><br/></div>
    <h2><?php _e('Manage versions', 'klasse-wp-poll-survey') ?>
        <?php if( $test_collection['post_status'] != 'publish' ): ?>
            <a href="?page=klasse-wp-poll-survey_edit&section=edit_version&post_parent=<?php echo $_REQUEST['id']; ?>" class="add-new-h2"><?php _e('New version'); ?></a>
        <?php endif; ?>
        <?php if( sizeof( $test_collection_publish_errors ) == 0 && sizeof( \kwps_classes\Version::get_all_by_post_parent($_REQUEST['id'] ) ) > 0  && $test_collection['post_status'] != 'publish' ):?>
            <a href="?page=klasse-wp-poll-survey_edit&section=edit_test_collection&action=publish&id=<?php echo $_REQUEST['id']; ?>" class="add-new-h2"><?php _e('Publish') ?></a>
        <?php else: ?>
            <p >Alle versies moeten correct zijn om te kunnen publiceren</p>
        <?php endif; ?>
        <p>
            You are currently managing versions for the test <b>'<?php echo $test_collection['post_title']; ?>' (<?php echo $test_collection['ID']; ?>).</b>
        </p>
    </h2>

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
    <?php elseif( $active_tab == 'settings'):?>
        <?php $allowed_dropdown_values = \kwps_classes\Test_Collection::$allowed_dropdown_values; ?>
        <form id="kwps-test-collection-settings" method="post" action="?page=klasse-wp-poll-survey_edit&id=<?php echo $_REQUEST['id']; ?>&section=edit_test_collection&tab=settings">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="kwps_logged_in_user_limit">Aangemelde gebruikers</label>
                    </th>
                    <td>
                        <select id="kwps_logged_in_user_limit" name="_kwps_logged_in_user_limit">
                            <?php foreach( $allowed_dropdown_values['_kwps_logged_in_user_limit'] as $value ): ?>
                                <?php $selected = ($settings['_kwps_logged_in_user_limit'] == $value ? 'selected' : '' ) ?>
                                <option value="<?php echo $value; ?>" <?php echo $selected?> ><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        <label for="kwps_logged_out_user_limit">Anonieme gebruikers</label>
                    </th>
                    <td>
                        <select id="kwps_logged_out_user_limit" name="_kwps_logged_out_user_limit">
                            <?php foreach( $allowed_dropdown_values['_kwps_logged_out_user_limit'] as $value ): ?>
                                <?php $selected = ($settings['_kwps_logged_out_user_limit'] == $value ? 'selected' : '' ) ?>
                                <option value="<?php echo $value; ?>" <?php echo $selected?> ><?php echo $value; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <?php
                        if( isset( $settings['_kwps_show_grouping_form'] ) && $settings['_kwps_show_grouping_form'] == 1 ) {
                            $checked = 'checked';
                        } else {
                            $checked = '';
                        }
                    ?>
                    <th scope="row">
                        <label for="kwps_show_grouping_form">Show grouping form</label>
                    </th>
                    <td>
                        <input id="kwps_show_grouping_form" type="checkbox" name="_kwps_show_grouping_form" value="1"<?php echo $checked; ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">

                    </th>
                    <td>
                        <div class="kwps kwps-single <?php if( isset( $outro['errors']['post_content'] ) )  echo ' kwps_error'; ?>" id="kwps-outro">
                            <h3>Collection Outro <?php if( in_array( 'post_content', $required_fields_coll_outro ) ) echo '<span class="kwps-required">*</span>' ?></h3>
                            <div class="inside">
                                <?php if( isset( $coll_outro['errors']['post_content'] ) ):?>
                                    <div class="error form-invalid below-h2">
                                        <p class=""><?php echo $coll_outro['errors']['post_content'] ?></p>
                                    </div>
                                <?php endif;?>
                                <a class="button kwps-add-result-button outro-result-button">Add result</a>
                                <?php if( isset( $coll_outro['ID'] ) ): ?>
                                    <input type="hidden" name="collection_outro[ID]" value="<?php echo $coll_outro['ID'];?>" class="kwps-single_input">
                                <?php endif;?>
                                <input type="hidden" name="collection_outro[post_parent]" value="<?php echo $test_collection['ID'];?>" class="kwps-single_input">
                                <input type="hidden" name="collection_outro[post_status]" value="draft" class="kwps-single_input">
                                <div class="kwps-content<?php if( isset( $coll_outro['errors']['post_content'] ) )  echo ' kwps_error'; ?>">
                                    <div style="display: none" class="kwps-content-editor">
                                        <?php wp_editor( (isset($coll_outro['post_content']))? $coll_outro['post_content'] : "Outro", 'collection_outro[post_content]', array('teeny' => true ) ); ?>
                                        <button class="kwps-content-editor-save">Save</button>
                                    </div>
                                    <div class="kwps-content-view">
                                        <div class="kwps-content-view-content">
                                            <?php echo (isset($coll_outro['post_content']))? $coll_outro['post_content'] : "Outro" ?>
                                        </div>
                                        <a class="kwps-content-edit button">Edit</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">
                        
                    </th>
                    <td>
                        <button type="submit" class="button button-primary">Opslaan</button>
                    </td>
                </tr>
            </table>
            
        </form>
    <?php else: ?>
        <?php
            $current_test_modus = \kwps_classes\Test_Collection::get_test_modus( $_REQUEST['id'] );
            $versions = \kwps_classes\Version::get_all_by_post_parent( $_REQUEST['id'] );
        ?>
        <p>
            <select id="showResultVersion">
                <option value="all">All Versions</option>
                <?php foreach( $versions as $version ):?>
                    <option value="<?php echo $version['ID']; ?>"><?php echo $version['post_title'];?></option>
                <?php endforeach;?>
            </select> <span>Select a version to see any result.</span>
        </p>

        <p>
            <select id="outputTypes">
                <?php foreach( $current_test_modus['_kwps_allowed_output_types_test_collection'] as $output_type ):?>
                    <option value="<?php echo $output_type; ?>"><?php echo $output_type;?></option>
                <?php endforeach;?>
            </select> <span>Select a version to see any result.</span>
        </p>
        
        <div id="kwps-results-container">
            
        </div>
    <?php endif; ?>

</div> <!-- .wrap -->
