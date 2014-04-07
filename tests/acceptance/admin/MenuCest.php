<?php
namespace admin;
use \WebGuy;

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
        $I->see('Poll & Survey', '#adminmenuwrap');
    }

}