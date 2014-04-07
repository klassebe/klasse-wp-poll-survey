<?php
namespace admin;

class AdminCommons
{
    public static $username = 'admin';
    public static $password = 'qbcdef';

    public static function logMeIn(\WebGuy $I)
    {
        $I->amOnPage('/wp-admin');
        $I->fillField('Username', self::$username);
        $I->fillField('Password',self::$password);
        $I->click('Log In');
    }

    public static function activate(\WebGuy $I)
    {
        self::logMeIn($I);
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.activate a');
    }

    public static function deactivate(\WebGuy $I)
    {
        self::logMeIn($I);
        $I->click('Plugins');
        $I->click('#klasse-wordpress-poll-survey span.deactivate a');
    }
}
?>