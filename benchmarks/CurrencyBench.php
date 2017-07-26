<?php

use Punic\Currency;

class CurrencyBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchCurrencyForTerritory()
    {
        Currency::getCurrencyForTerritory('US');
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchName()
    {
        Currency::getName('USD');
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchCurrencyHistoryForTerritory()
    {
        Currency::getCurrencyHistoryForTerritory('IT');
    }

}
