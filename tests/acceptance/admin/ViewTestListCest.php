<?php
namespace admin;
use \WebGuy;

class ViewTestListCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }

    // tests
    public function tryToTest(WebGuy $I)
    {
        AdminCommons::logMeIn($I);
        $I->wantTo('see an overview of the tests');
        $I->click('Poll & Survey', '#toplevel_page_klasse-wp-poll-survey_tests');
        $I->see('Tests', '.wrap');
    }

}