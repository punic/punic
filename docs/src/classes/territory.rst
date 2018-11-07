*************************
``Punic\Territory`` class
*************************

The territory class contains several methods to work with regions, countries and continents.

.. code-block:: php

    use Punic\Territory;
     
    echo Territory::getName('US', 'en');
    // Output: United States
     
    $countries = Territory::getCountries();
    print_r($countries);
    // Output: the list of all countries, indexed by the territory code
     
    $continents = Territory::getContinents();
    print_r($continents);
    // Output: the list of all continents
     
    $continentsAndCountries = Territory::getContinentsAndCountries();
    print_r($continentsAndCountries);
    // Output: the list of all countries grouped by continent
