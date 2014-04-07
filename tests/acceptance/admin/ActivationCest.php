<?php
namespace admin;
use \WebGuy;

/**
 * @guy WebGuy\MemberSteps
 */
class ActivationCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    function activatePlugin(WebGuy\MemberSteps $I)
    {
        $I->login();
        $I->wantTo('activate the plugin');
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.activate a');
        $I->see('Deactivate', '#klasse-wordpress-poll-survey span.deactivate a');
    }

    function deactivatePlugin(WebGuy\MemberSteps $I)
    {
        $I->login();
        $I->wantTo('de-activate the plugin');
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.deactivate a');
        $I->see('Activate','#klasse-wordpress-poll-survey span.activate a');
    }
}