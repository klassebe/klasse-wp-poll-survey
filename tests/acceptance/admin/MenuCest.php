<?php
namespace admin;
use \WebGuy;
use pages\AdminMenuPage as AdminMenuPage;

/**
 * @guy WebGuy\MemberSteps
 */
class MenuCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    public function checkMenu(WebGuy\MemberSteps $I)
    {
        $I->activate_kwps();
        $I->wantTo('see the menu item');
        $I->see(AdminMenuPage::$kwps_full_name, AdminMenuPage::$kwps_full_name_selector);
    }

}