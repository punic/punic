<?php

use Punic\Territory;

class PunicBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchCountries()
    {
        Territory::getCountries();
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchContinents()
    {
        Territory::getContinents();
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchContinentsAndCountries()
    {
        Territory::getContinentsAndCountries();
    }
}
