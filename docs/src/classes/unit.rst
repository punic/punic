********************
``Punic\Unit`` class
********************

Whether you need an abbreviation or a long form of a number with a unit, use this class.
Properly formatted numbers and units in every the language.

.. code-block:: php

    use Punic\Unit;
     
    echo Unit::format(2.0123, 'millisecond', 2, 'it');
    // Output: 2,01 ms
     
    echo Unit::format(2.0123, 'millisecond', 'long,1', 'it');
    // Output: 2,0 millisecondi
     
    echo Unit::format(2.0123, 'millisecond', 'narrow', 'it');
    // Output: 2,0123 ms
