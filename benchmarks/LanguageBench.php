<?php

use Punic\Language;

class LanguageBench
{
    /**
     * @Revs(100)
     * @Iterations(5)
     */
    public function benchName()
    {
        Language::getName('it_IT', 'en_US');
    }

}
