<?php
namespace admin;

class AdminCommons
{
    public static $username = 'admin';
    public static $password = 'qbcdef';

    public static function logMeIn($I)
    {
        $I->amOnPage('/wp-admin');
        $I->fillField('Username', self::$username);
        $I->fillField('Password',self::$password);
        $I->click('Log In');
    }

    public static function activate($I)
    {
        self::logMeIn($I);
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.activate a');
    }
}
?>