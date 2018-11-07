************************
``Punic\Currency`` class
************************

Need to know the currency used in a country?
Do you want to have a list of all the currencies and their localized name?
Use this class!

.. code-block:: php

    use Punic\Currency;
     
    echo Currency::getCurrencyForTerritory('US');
    // Output: USD (the currency code for US Dollars)
     
    echo Currency::getName('USD');
    // Output: the name of USD in the current default Punic locale
     
    var_export(Currency::getCurrencyHistoryForTerritory('IT'));
    // Output: the history of currencies used in Italy (from Italian Lira to Euro)
