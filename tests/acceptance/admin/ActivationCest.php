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

    protected function login(WebGuy $I)
    {
        $I->amOnPage('/wp-admin');
        $I->fillField('Username', 'admin');
        $I->fillField('Password','');
        $I->click('Log In');
    }

    /**
     * @before login
     */
    function activatePlugin(WebGuy $I)
    {
        $I->wantTo('activate the plugin');
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.activate a');
        $I->see('Deactivate');
    }

    /**
     * @before login
     */
    function deactivatePlugin(WebGuy $I)
    {
        $I->wantTo('de-activate the plugin');
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.deactivate a');
        $I->see('Activate');
    }
}