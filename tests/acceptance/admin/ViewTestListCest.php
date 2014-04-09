<?php
namespace admin;
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
//        $I->see('Bulk Actions', 'option');
        $I->see('Bulk Actions', 'option[value=-1]');
        $I->see('Delete', 'option[value=delete]');
        $I->see('No items found.');
        $I->see('Title');
        $I->see('Views');
        $I->see('Date');
        $I->see('Tests', 'h2');
    }

}