**********************
``Punic\Number`` class
**********************

The number class offers methods to convert a numerical variable into a properly formatted string.
After all, a string like 1,234.56 is easier to read than 1234.56.

.. code-block:: php

    use Punic\Number;
     
    echo Number::format(1234.567, 2, 'it');
    // Output: 1.234,57
     
    echo Number::format(1234.567, 2, 'en');
    // Output: 1,234.57
     
    echo Number::unformat('1.234,56', 'it');
    // Output: 1234.56
     
    echo Number::unformat('1,234.56', 'en');
    // Output: 1234.56
