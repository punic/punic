<?php

use Punic\Phone;

class PhoneBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchPrefixesForTerritory()
    {
        Phone::getPrefixesForTerritory('US');
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchTerritoriesForPrefix()
    {
        Phone::getTerritoriesForPrefix(1);
    }
}
