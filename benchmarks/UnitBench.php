<?php

use Punic\Unit;

class UnitBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchFormat()
    {
        Unit::format('2.0123', 'millisecond', '1', 'en');
    }

}
