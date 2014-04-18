<?php
namespace WebGuy;
use pages\AdminMenuPage as AdminMenuPage;
use pages\PluginsPage;


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
        $I->click(AdminMenuPage::$plugins);
        $I->click(PluginsPage::$activate_kwps_selector);
    }

    public function deactivate_kwps()
    {
        $I = $this;
        $I->login();
        $I->click(AdminMenuPage::$plugins);
        $I->click('#klasse-wordpress-poll-survey span.deactivate a');
    }

}