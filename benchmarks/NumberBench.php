<?php

use Punic\Number;

class NumberBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchFormat()
    {
        Number::format(1234.567, 2, 'it');
    }

    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchUnformat()
    {
        Number::unformat('1,234.56', 'en');
    }
}
