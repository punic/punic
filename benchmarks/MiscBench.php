<?php

use Punic\Misc;

class MiscBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchJoinUnits()
    {
        Misc::joinUnits(array('Uno', 'due'), '', 'it');
    }

}
