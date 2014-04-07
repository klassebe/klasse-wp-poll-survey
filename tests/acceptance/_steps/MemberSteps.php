<?php
namespace WebGuy;

class MemberSteps extends \WebGuy
{
    public static $username = 'admin';
    public static $password = 'qbcdef';

    function login()
    {
        $I = $this;
        $I->amOnPage('/wp-admin');
        $I->fillField('Username', self::$username);
        $I->fillField('Password',self::$password);
        $I->click('Log In');

    }

    public function activate_kwps()
    {
        $I = $this;
        $I->login();
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.activate a');
    }

    public function deactivate_kwps()
    {
        $I = $this;
        $I->login();
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.deactivate a');
    }

}