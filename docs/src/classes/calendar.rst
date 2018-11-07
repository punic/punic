************************
``Punic\Calendar`` class
************************

The calendar class contains a number of methods to localize date object related outputs.
``formatDate`` to format a date, ``getEraName`` to return the name of an era, ``getMonthName`` to do the same for months.
You can also find some helper methods like ``toDateTime`` to convert a date/time representation to a ``DateTime`` object.

.. code-block:: php

    use Punic\Calendar;

    include 'vendor/autoload.php';

    $dt = Calendar::toDateTime('2010-03-07');

    echo Calendar::getEraName(1);
    // Output: AD

    echo Calendar::getMonthName($dt, 'abbreviated');
    // Output: Mar

    echo Calendar::getMonthName($dt, 'wide', 'it');
    // Output: marzo

    echo Calendar::getWeekdayName(1);
    // Output: Monday

    echo Calendar::getQuarterName(1);
    // Output: 1st quarter

    echo Calendar::format($dt, 'd MMMM y', 'de');
    // Output: 7 März 2010

    echo Calendar::formatDate($dt, 'full', 'it');
    // Output: domenica 7 marzo 2010

    echo Calendar::formatDate($dt, '~yMd', 'da');
    // Output: 7/3/2010

    $dt2 = Calendar::toDateTime('2010-03-10');
    echo Calendar::formatInterval($dt, $dt2, 'yMMMd');
    // Output: Mar 7 – 10, 2010
