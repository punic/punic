************************
``Punic\Language`` class
************************

The language class helps you to convert a locale code into a language name.
Want to know what it_IT is called in American English?
Use this class to show all the languages in the right way.

.. code-block:: php

    use Punic\Language;
     
    echo Language::getName('it_IT', 'en_US');
    // Output: Italian (Italy)
     
    echo Language::getName('de_CH', 'de_DE');
    // Output: Schweizer Hochdeutsch (Schweiz)
