<?php

use Punic\Plural;

class PluralBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchRule()
    {
        Plural::getRule(2, 'en');
    }

}
