<?php
namespace admin;
use pages\TestListView;
use \WebGuy;
use pages\AdminMenuPage as AdminMenuPage;

/**
 * @guy WebGuy\MemberSteps
 */
class ViewTestListCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function tryToTest(WebGuy\MemberSteps $I)
    {
        $I->activate_kwps();
        $I->wantTo('see an overview of the tests');
        $I->click('Poll & Survey', '#toplevel_page_klasse-wp-poll-survey_tests');

//        'sub'-header elements
        $I->see(TestListView::$title, TestListView::$title_selector);
        $I->see(TestListView::$add_test, TestListView::$add_test_selector);

//        filters
        $I->see(TestListView::$show_all, TestListView::$show_all_selector);
        $I->see(TestListView::$show_published, TestListView::$show_published_selector);
        $I->see(TestListView::$show_archived, TestListView::$show_archived_selector);
        $I->see(TestListView::$show_drafts, TestListView::$show_drafts_selector);
        $I->see(TestListView::$show_trash, TestListView::$show_trash_selector);

//        bulk actions (above list)
//        $I->see(TestListView::$dropdown_bulk_actions_selector)

        $I->see('Delete', 'option[value=delete]');
        $I->see('No items found.');
        $I->see('Title');
        $I->see('Views');
        $I->see('Date');
        $I->see('Tests', 'h2');
    }



}