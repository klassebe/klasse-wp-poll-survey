<?php

use RedBean_Facade as R;

class Poll
{
    public function install()
    {
        $b = R::dispense( 'mode' );
        $b->name = 'Poll';
        $b->description = 'This is the poll';
        R::store($b);
    }
}