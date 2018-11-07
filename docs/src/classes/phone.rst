*********************
``Punic\Phone`` class
*********************

Need to know the country calling codes of a country?
Need to know the countries associated to a country calling code?
Use this class!

.. code-block:: php

    use Punic\Phone;
     
    var_export(Phone::getPrefixesForTerritory('US'));
    // Output: an array containing '1', the country calling code for US
     
    var_export(Phone::getTerritoriesForPrefix('1'));
    // Output: an array containing the country codes having '1' as country calling code
    // (for instance US, CA)
