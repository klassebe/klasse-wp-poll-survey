<?php
namespace admin;
use \WebGuy;

class MenuCest
{

    public function _before()
    {
    }

    public function _after()
    {
    }
    public function checkMenu(WebGuy $I)
    {
        AdminCommons::activate($I);
        $I->wantTo('see the menu item');
        $I->see('Poll & Survey', '#adminmenuwrap');
    }

}