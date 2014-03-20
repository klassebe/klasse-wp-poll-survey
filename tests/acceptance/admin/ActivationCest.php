<?php
namespace admin;
use \WebGuy;

class ActivationCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    function activatePlugin(WebGuy $I)
    {
        AdminCommons::logMeIn($I);
        $I->wantTo('activate the plugin');
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.activate a');
        $I->see('Deactivate');
    }

    function deactivatePlugin(WebGuy $I)
    {
        AdminCommons::logMeIn($I);
        $I->wantTo('de-activate the plugin');
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.deactivate a');
        $I->see('Activate');
    }
}