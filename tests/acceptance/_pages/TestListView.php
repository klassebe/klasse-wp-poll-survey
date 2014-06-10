<?php

namespace pages;

class TestListView {
//    'sub'-header elements
    static $title = 'Tests';
    static $title_selector = 'h2';
    static $add_test = 'add test';
    static $add_test_selector = 'a #add-new-h2';

    // filters
    static $show_all = 'All';
    static $show_all_selector = 'a #all';

    static $show_published = 'Published';
    static $show_published_selector = 'a #publish';

    static $show_archived = 'Archived';
    static $show_archived_selector = 'a #archive';

    static $show_drafts = 'Drafts';
    static $show_drafts_selector = 'a #drafts';

    static $show_trash = 'Trash';
    static $show_trash_selector = 'a #trash';

    static $dropdown_bulk_actions_selector = 'select[name=action]';

    // Bulk action values for dropdown (above list):
    static $option_bulk_actions = 'Bulk actions';
    static $option_bulk_actions_selector = 'option[value=-1]';

    static $option_publish = 'Publish';
    static $option_publish_selector = 'option[value=publish]';
    static $option_delete = 'Delete';
    static $option_delete_selector = 'option[value=delete]';

    static $option_apply_bulk_actions = 'Apply';
    static $option_apply_bulk_actions_selector = 'input[type=submit][value=Apply][id=doaction]';

    // Bulk action values for dropdown (below list):
    static $option_bulk_actions2 = 'Bulk actions';
    static $option_bulk_actions2_selector = 'option[value=-1]';

    static $option2_publish = 'Publish';
    static $option2_publish_selector = 'option[value=publish]';
    static $option2_delete = 'Delete';
    static $option2_delete_selector = 'option[value=delete]';

    static $option2_apply_bulk_actions = 'Apply';
    static $option2_apply_bulk_actions_selector = 'input[type=submit][value=Apply][id=doaction2]';


//    Date filter options
    static $period_filter_dropdown_selector = 'select[name=period]';

    static $period_filter_option_all = 'Show all dates';
    static $period_filter_option_all_selector = 'option[value=-1]';

    static $period_filter_option_week = 'Past week';
    static $period_filter_option_week_selector = 'option[value=week]';

    static $period_filter_option_month = 'Past month';
    static $period_filter_option_month_selector = 'option[value=month]';

    static $period_filter_option_half_year = 'Past 6 months';
    static $period_filter_option_half_year_selector = 'option[value=6months]';

    static $period_filter_option_year = 'Past year';
    static $period_filter_option_year_selector = 'option[value=year]';


//    Type filter options
    static $type_filter_dropdown_selector='select[name=testmodus]';

    static $type_filter_option_all = 'View all testmodi';
    static $type_filter_option_all_selector = 'option[value=-1]';

    static $type_filter_option_poll = 'Polls';
    static $type_filter_option_poll_selector = 'option[value=poll]';

//      Search field and button
    static $search_field_selector = 'input[type=search][id=post-search-input][name=search]';
    static $search_button = 'input[type=submit][value=Search Polls][id=search-submit]';

//    Column titles
    static $col_title_title = 'Title';
    static $col_title_author = 'Author';
    static $col_title_test_modus = 'Testmodus';
    static $col_title_nr_of_entries = '# entries';
    static $col_title_nr_of_views = '# views';
    static $col_title_conversion = 'Conversion';
    static $col_title_date = 'Date';
}