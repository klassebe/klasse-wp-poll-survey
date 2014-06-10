<?php

use RedBean_Facade as R;

class PersonalityTest
{
    public function install()
    {
        $b = R::dispense( 'mode' );
        $b->name = 'Personality Test';
        $b->description = 'This is the Personality Test';
        R::store($b);
    }
}