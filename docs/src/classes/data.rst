********************
``Punic\Data`` class
********************

If you work with a larger application, you probably don't want to specify the locale for each string you want to localize.

You can use the ``Data`` class to set a globally active locale and then skip the locale parameter when calling a method to localize a string.

.. code-block:: php

    use Punic\Data;
    use Punic\Language;
 
    Data::setDefaultLocale('de_DE');
    echo Language::getName('de_CH');
    // Output: Schweizer Hochdeutsch (Schweiz)
    // without specifying a second parameter for Language::getName()
 
    Data::setFallbackLocale('it_IT');
    echo Language::getName('de_CH', 'et_INVALID');
    // Output: alto tedesco svizzero (Svizzera)
    // because the value in the second parameter of Language::getName() isn't a valid locale
