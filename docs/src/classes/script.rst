*************************
``Punic\Script`` class
*************************

The Script class contains several methods to work with Scripts (Script is the Unicode term that defines how languages are written, like `Latin` or `Traditional Han`).

.. code-block:: php

    use Punic\Script;
    
    var_dump(Territory::getAllScriptCodes());
    // Output: the identifiers of all the available scripts
    
    var_dump(Territory::getAvailableScriptCodes('en'));
    // Output: the identifiers of all the scripts that have available translations in a specific language
    
    echo Script::getScriptName('Hant');
    // Output: the translated name of the Hant script
    
    var_dump(Territory::getAllScripts());
    // Output: an array whose array are the script identifiers and the values are the translated script names
