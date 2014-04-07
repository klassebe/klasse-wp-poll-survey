<?php
namespace admin;
use pages\PluginsPage as PluginsPage;
use pages\AdminMenuPage as AdminMenu;
use \WebGuy\MemberSteps as MemberSteps;

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

    function activatePlugin(MemberSteps $I)
    {
        $I->login();
        $I->wantTo('activate the plugin');
        $I->click(AdminMenu::$plugins);
//        $I->click('Plugins');
        $I->amOnPage(PluginsPage::$URL);
        $I->click(PluginsPage::$activate_kwps_selector);
        $I->see(PluginsPage::$deactivate, PluginsPage::$deactivate_kwps_selector);

    }

    function deactivatePlugin(MemberSteps $I)
    {
        $I->activate_kwps();

        $I->wantTo('de-activate the plugin');
        $I->click(AdminMenu::$plugins);
        $I->amOnPage(PluginsPage::$URL);
        $I->click(PluginsPage::$deactivate_kwps_selector);
        $I->see(PluginsPage::$activate,PluginsPage::$activate_kwps_selector);
    }
}