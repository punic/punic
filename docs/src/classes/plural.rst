**********************
``Punic\Plural`` class
**********************

Need to know the plural of a number? Use this class. 

.. code-block:: php

    use Punic\Plural;
     
    echo Plural::getRule(1, 'en');
    // Output: one
     
    echo Plural::getRule(2, 'en');
    // Output: other
