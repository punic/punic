
window.projectVersion = 'master';

(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:Punic" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Punic.html">Punic</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:Punic_Exception" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="Punic/Exception.html">Exception</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:Punic_Exception_BadArgumentType" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/BadArgumentType.html">BadArgumentType</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_BadDataFileContents" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/BadDataFileContents.html">BadDataFileContents</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_DataFileNotFound" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/DataFileNotFound.html">DataFileNotFound</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_DataFileNotReadable" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/DataFileNotReadable.html">DataFileNotReadable</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_DataFolderNotFound" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/DataFolderNotFound.html">DataFolderNotFound</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_InvalidDataFile" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/InvalidDataFile.html">InvalidDataFile</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_InvalidLocale" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/InvalidLocale.html">InvalidLocale</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_InvalidOverride" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/InvalidOverride.html">InvalidOverride</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_NotImplemented" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/NotImplemented.html">NotImplemented</a>                    </div>                </li>                            <li data-name="class:Punic_Exception_ValueNotInList" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="Punic/Exception/ValueNotInList.html">ValueNotInList</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:Punic_Calendar" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Calendar.html">Calendar</a>                    </div>                </li>                            <li data-name="class:Punic_Comparer" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Comparer.html">Comparer</a>                    </div>                </li>                            <li data-name="class:Punic_Currency" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Currency.html">Currency</a>                    </div>                </li>                            <li data-name="class:Punic_Data" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Data.html">Data</a>                    </div>                </li>                            <li data-name="class:Punic_Exception" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Exception.html">Exception</a>                    </div>                </li>                            <li data-name="class:Punic_Language" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Language.html">Language</a>                    </div>                </li>                            <li data-name="class:Punic_Misc" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Misc.html">Misc</a>                    </div>                </li>                            <li data-name="class:Punic_Number" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Number.html">Number</a>                    </div>                </li>                            <li data-name="class:Punic_Phone" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Phone.html">Phone</a>                    </div>                </li>                            <li data-name="class:Punic_Plural" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Plural.html">Plural</a>                    </div>                </li>                            <li data-name="class:Punic_Territory" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Territory.html">Territory</a>                    </div>                </li>                            <li data-name="class:Punic_Unit" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="Punic/Unit.html">Unit</a>                    </div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "Punic.html", "name": "Punic", "doc": "Namespace Punic"},{"type": "Namespace", "link": "Punic/Exception.html", "name": "Punic\\Exception", "doc": "Namespace Punic\\Exception"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Calendar.html", "name": "Punic\\Calendar", "doc": "&quot;Date and time related functions.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_toDateTime", "name": "Punic\\Calendar::toDateTime", "doc": "&quot;Convert a date\/time representation to a {@link http:\/\/php.net\/manual\/class.datetime.php \\DateTime} instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_convertPhpToIsoFormat", "name": "Punic\\Calendar::convertPhpToIsoFormat", "doc": "&quot;Converts a format string from {@link http:\/\/php.net\/manual\/en\/function.date.php#refsect1-function.date-parameters PHP&#039;s date format} to {@link http:\/\/www.unicode.org\/reports\/tr35\/tr35-dates.html#Date_Field_Symbol_Table ISO format}.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_tryConvertIsoToPhpFormat", "name": "Punic\\Calendar::tryConvertIsoToPhpFormat", "doc": "&quot;Try to convert a date, time or date\/time {@link http:\/\/www.unicode.org\/reports\/tr35\/tr35-dates.html#Date_Field_Symbol_Table ISO format string} to a {@link http:\/\/php.net\/manual\/en\/function.date.php#refsect1-function.date-parameters PHP date\/time format}.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getEraName", "name": "Punic\\Calendar::getEraName", "doc": "&quot;Get the name of an era.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getMonthName", "name": "Punic\\Calendar::getMonthName", "doc": "&quot;Get the name of a month.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getWeekdayName", "name": "Punic\\Calendar::getWeekdayName", "doc": "&quot;Get the name of a week day.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getQuarterName", "name": "Punic\\Calendar::getQuarterName", "doc": "&quot;Get the name of a quarter.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getDayperiodName", "name": "Punic\\Calendar::getDayperiodName", "doc": "&quot;Get the name of a day period (AM\/PM).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getVariableDayperiodName", "name": "Punic\\Calendar::getVariableDayperiodName", "doc": "&quot;Get the name of a variable day period (\&quot;morning\&quot;, \&quot;afternoon\&quot;, etc.).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimezoneNameNoLocationSpecific", "name": "Punic\\Calendar::getTimezoneNameNoLocationSpecific", "doc": "&quot;Returns the localized name of a timezone, no location-specific.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimezoneNameLocationSpecific", "name": "Punic\\Calendar::getTimezoneNameLocationSpecific", "doc": "&quot;Returns the localized name of a timezone, location-specific.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimezoneExemplarCity", "name": "Punic\\Calendar::getTimezoneExemplarCity", "doc": "&quot;Returns the localized name of an exemplar city for a specific timezone.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_has12HoursClock", "name": "Punic\\Calendar::has12HoursClock", "doc": "&quot;Returns true if a locale has a 12-hour clock, false if 24-hour clock.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getFirstWeekday", "name": "Punic\\Calendar::getFirstWeekday", "doc": "&quot;Retrieve the first weekday for a specific locale (from 0-Sunday to 6-Saturnday).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getSortedWeekdays", "name": "Punic\\Calendar::getSortedWeekdays", "doc": "&quot;Returns the sorted list of weekdays, starting from {@link getFirstWeekday}.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getDateFormat", "name": "Punic\\Calendar::getDateFormat", "doc": "&quot;Get the ISO format for a date.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimeFormat", "name": "Punic\\Calendar::getTimeFormat", "doc": "&quot;Get the ISO format for a time.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getDatetimeFormat", "name": "Punic\\Calendar::getDatetimeFormat", "doc": "&quot;Get the ISO format for a date\/time.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getSkeletonFormat", "name": "Punic\\Calendar::getSkeletonFormat", "doc": "&quot;Get the ISO format based on a skeleton.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getIntervalFormat", "name": "Punic\\Calendar::getIntervalFormat", "doc": "&quot;Get the ISO format for a date\/time interval.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getDeltaDays", "name": "Punic\\Calendar::getDeltaDays", "doc": "&quot;Returns the difference in days between two dates (or between a date and today).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_describeInterval", "name": "Punic\\Calendar::describeInterval", "doc": "&quot;Describe an interval between two dates (eg &#039;2 days and 4 hours&#039;).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatDate", "name": "Punic\\Calendar::formatDate", "doc": "&quot;Format a date.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatDateEx", "name": "Punic\\Calendar::formatDateEx", "doc": "&quot;Format a date (extended version: various date\/time representations - see toDateTime()).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatTime", "name": "Punic\\Calendar::formatTime", "doc": "&quot;Format a time.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatTimeEx", "name": "Punic\\Calendar::formatTimeEx", "doc": "&quot;Format a time (extended version: various date\/time representations - see toDateTime()).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatDatetime", "name": "Punic\\Calendar::formatDatetime", "doc": "&quot;Format a date\/time.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatDatetimeEx", "name": "Punic\\Calendar::formatDatetimeEx", "doc": "&quot;Format a date\/time (extended version: various date\/time representations - see toDateTime()).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatInterval", "name": "Punic\\Calendar::formatInterval", "doc": "&quot;Format a date\/time interval.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatIntervalEx", "name": "Punic\\Calendar::formatIntervalEx", "doc": "&quot;Format a date\/time interval (extended version: various date\/time representations - see toDateTime()).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_format", "name": "Punic\\Calendar::format", "doc": "&quot;Format a date and\/or time.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_formatEx", "name": "Punic\\Calendar::formatEx", "doc": "&quot;Format a date and\/or time (extended version: various date\/time representations - see toDateTime()).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getDateRelativeName", "name": "Punic\\Calendar::getDateRelativeName", "doc": "&quot;Retrieve the relative day name (eg &#039;yesterday&#039;, &#039;tomorrow&#039;), if available.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getDatetimeFormatReal", "name": "Punic\\Calendar::getDatetimeFormatReal", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getDatetimeWidth", "name": "Punic\\Calendar::getDatetimeWidth", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getBestMatchingSkeleton", "name": "Punic\\Calendar::getBestMatchingSkeleton", "doc": "&quot;Rudimentary implementation of skeleton matching algorithm in #UTS 35, part 2, section 2.6.2.1.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_preprocessSkeleton", "name": "Punic\\Calendar::preprocessSkeleton", "doc": "&quot;Replace special input skeleton fields (j, J, C) with locale-specific substitutions.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_postprocessSkeletonFormat", "name": "Punic\\Calendar::postprocessSkeletonFormat", "doc": "&quot;Replace special input skeleton fields, adjust field widths, and add second fraction to format pattern.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getGreatestDifference", "name": "Punic\\Calendar::getGreatestDifference", "doc": "&quot;Return the most significant field where the two dates differ. For fractional seconds,\n&#039;S&#039; is returned if the differ on the first decimal, &#039;SS&#039; for the second decimal etc.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_adjustGreatestDifference", "name": "Punic\\Calendar::adjustGreatestDifference", "doc": "&quot;Adjust greatest difference to the fields used in the skeleton.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_splitIntervalFormat", "name": "Punic\\Calendar::splitIntervalFormat", "doc": "&quot;Splits an interval format into two datetime formats.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeDayOfWeek", "name": "Punic\\Calendar::decodeDayOfWeek", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeDayOfWeekLocal", "name": "Punic\\Calendar::decodeDayOfWeekLocal", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeDayOfWeekLocalAlone", "name": "Punic\\Calendar::decodeDayOfWeekLocalAlone", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeDayOfMonth", "name": "Punic\\Calendar::decodeDayOfMonth", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeMonth", "name": "Punic\\Calendar::decodeMonth", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeMonthAlone", "name": "Punic\\Calendar::decodeMonthAlone", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeYear", "name": "Punic\\Calendar::decodeYear", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeHour12", "name": "Punic\\Calendar::decodeHour12", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeDayperiod", "name": "Punic\\Calendar::decodeDayperiod", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeVariableDayperiod", "name": "Punic\\Calendar::decodeVariableDayperiod", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeHour24", "name": "Punic\\Calendar::decodeHour24", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeHour12From0", "name": "Punic\\Calendar::decodeHour12From0", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeHour24From1", "name": "Punic\\Calendar::decodeHour24From1", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeMinute", "name": "Punic\\Calendar::decodeMinute", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeSecond", "name": "Punic\\Calendar::decodeSecond", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeTimezoneNoLocationSpecific", "name": "Punic\\Calendar::decodeTimezoneNoLocationSpecific", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeTimezoneShortGMT", "name": "Punic\\Calendar::decodeTimezoneShortGMT", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeEra", "name": "Punic\\Calendar::decodeEra", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeYearWeekOfYear", "name": "Punic\\Calendar::decodeYearWeekOfYear", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeYearExtended", "name": "Punic\\Calendar::decodeYearExtended", "doc": "&quot;Note: we assume Gregorian calendar here.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeYearRelatedGregorian", "name": "Punic\\Calendar::decodeYearRelatedGregorian", "doc": "&quot;Note: we assume Gregorian calendar here.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeQuarter", "name": "Punic\\Calendar::decodeQuarter", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeQuarterAlone", "name": "Punic\\Calendar::decodeQuarterAlone", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeWeekOfYear", "name": "Punic\\Calendar::decodeWeekOfYear", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeDayOfYear", "name": "Punic\\Calendar::decodeDayOfYear", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeWeekdayInMonth", "name": "Punic\\Calendar::decodeWeekdayInMonth", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeFractionsOfSeconds", "name": "Punic\\Calendar::decodeFractionsOfSeconds", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeMsecInDay", "name": "Punic\\Calendar::decodeMsecInDay", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeTimezoneDelta", "name": "Punic\\Calendar::decodeTimezoneDelta", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeTimezoneNoLocationGeneric", "name": "Punic\\Calendar::decodeTimezoneNoLocationGeneric", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeTimezoneID", "name": "Punic\\Calendar::decodeTimezoneID", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeTimezoneWithTime", "name": "Punic\\Calendar::decodeTimezoneWithTime", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeTimezoneWithTimeZ", "name": "Punic\\Calendar::decodeTimezoneWithTimeZ", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeWeekOfMonth", "name": "Punic\\Calendar::decodeWeekOfMonth", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeYearCyclicName", "name": "Punic\\Calendar::decodeYearCyclicName", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodeModifiedGiulianDay", "name": "Punic\\Calendar::decodeModifiedGiulianDay", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimezoneCanonicalID", "name": "Punic\\Calendar::getTimezoneCanonicalID", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_decodePunicExtension", "name": "Punic\\Calendar::decodePunicExtension", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimezoneNameFromDatetime", "name": "Punic\\Calendar::getTimezoneNameFromDatetime", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimezoneNameFromTimezone", "name": "Punic\\Calendar::getTimezoneNameFromTimezone", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_getTimezoneLocationFromDatetime", "name": "Punic\\Calendar::getTimezoneLocationFromDatetime", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Calendar", "fromLink": "Punic/Calendar.html", "link": "Punic/Calendar.html#method_tokenizeFormat", "name": "Punic\\Calendar::tokenizeFormat", "doc": "&quot;Tokenize an ISO date\/time format string.&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Comparer.html", "name": "Punic\\Comparer", "doc": "&quot;Various helper stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Comparer", "fromLink": "Punic/Comparer.html", "link": "Punic/Comparer.html#method___construct", "name": "Punic\\Comparer::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Comparer", "fromLink": "Punic/Comparer.html", "link": "Punic/Comparer.html#method_compare", "name": "Punic\\Comparer::compare", "doc": "&quot;Compare two strings.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Comparer", "fromLink": "Punic/Comparer.html", "link": "Punic/Comparer.html#method_sort", "name": "Punic\\Comparer::sort", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Currency.html", "name": "Punic\\Currency", "doc": "&quot;Currency-related stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getAllCurrencies", "name": "Punic\\Currency::getAllCurrencies", "doc": "&quot;Returns all the currencies.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getName", "name": "Punic\\Currency::getName", "doc": "&quot;Returns the name of a currency given its code.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getSymbol", "name": "Punic\\Currency::getSymbol", "doc": "&quot;Returns the name of a currency given its code.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getNumericCode", "name": "Punic\\Currency::getNumericCode", "doc": "&quot;Returns the ISO 4217 code for a currency given its currency code.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getByNumericCode", "name": "Punic\\Currency::getByNumericCode", "doc": "&quot;Returns the currency code given its ISO 4217 code.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getCurrencyHistoryForTerritory", "name": "Punic\\Currency::getCurrencyHistoryForTerritory", "doc": "&quot;Return the history for the currencies used in a territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getCurrencyForTerritory", "name": "Punic\\Currency::getCurrencyForTerritory", "doc": "&quot;Return the currency to be used in a territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Currency", "fromLink": "Punic/Currency.html", "link": "Punic/Currency.html#method_getLocaleData", "name": "Punic\\Currency::getLocaleData", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Data.html", "name": "Punic\\Data", "doc": "&quot;Common data helper stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getDefaultLocale", "name": "Punic\\Data::getDefaultLocale", "doc": "&quot;Return the current default locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getDefaultLanguage", "name": "Punic\\Data::getDefaultLanguage", "doc": "&quot;Return the current default language.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_setDefaultLocale", "name": "Punic\\Data::setDefaultLocale", "doc": "&quot;Set the current default locale and language.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getFallbackLocale", "name": "Punic\\Data::getFallbackLocale", "doc": "&quot;Return the current fallback locale (used if default locale is not found).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getFallbackLanguage", "name": "Punic\\Data::getFallbackLanguage", "doc": "&quot;Return the current fallback language (used if default locale is not found).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_setFallbackLocale", "name": "Punic\\Data::setFallbackLocale", "doc": "&quot;Set the current fallback locale and language.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getOverrides", "name": "Punic\\Data::getOverrides", "doc": "&quot;Get custom overrides of CLDR locale data.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_setOverrides", "name": "Punic\\Data::setOverrides", "doc": "&quot;Set custom overrides of CLDR locale data.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getOverridesGeneric", "name": "Punic\\Data::getOverridesGeneric", "doc": "&quot;Get custom overrides of CLDR generic data.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_setOverridesGeneric", "name": "Punic\\Data::setOverridesGeneric", "doc": "&quot;Set custom overrides of CLDR locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getDataDirectory", "name": "Punic\\Data::getDataDirectory", "doc": "&quot;Get the data root directory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_setDataDirectory", "name": "Punic\\Data::setDataDirectory", "doc": "&quot;Set the data root directory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_get", "name": "Punic\\Data::get", "doc": "&quot;Get the locale data.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getGeneric", "name": "Punic\\Data::getGeneric", "doc": "&quot;Get the generic data.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getAvailableLocales", "name": "Punic\\Data::getAvailableLocales", "doc": "&quot;Return a list of available locale identifiers.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_guessFullLocale", "name": "Punic\\Data::guessFullLocale", "doc": "&quot;Try to guess the full locale (with script and territory) ID associated to a language.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getTerritory", "name": "Punic\\Data::getTerritory", "doc": "&quot;Return the terrotory associated to the locale (guess it if it&#039;s not present in $locale).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getTerritoryNode", "name": "Punic\\Data::getTerritoryNode", "doc": "&quot;Return the node associated to the locale territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getLanguageNode", "name": "Punic\\Data::getLanguageNode", "doc": "&quot;Return the node associated to the language (not locale) territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getLocaleItem", "name": "Punic\\Data::getLocaleItem", "doc": "&quot;Returns the item of an array associated to a locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_explodeLocale", "name": "Punic\\Data::explodeLocale", "doc": "&quot;Parse a string representing a locale and extract its components.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getParentTerritory", "name": "Punic\\Data::getParentTerritory", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_expandTerritoryGroup", "name": "Punic\\Data::expandTerritoryGroup", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getLocaleFolder", "name": "Punic\\Data::getLocaleFolder", "doc": "&quot;Returns the path of the locale-specific data, looking also for the fallback locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_getLocaleAlternatives", "name": "Punic\\Data::getLocaleAlternatives", "doc": "&quot;Returns a list of locale identifiers associated to a locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Data", "fromLink": "Punic/Data.html", "link": "Punic/Data.html#method_merge", "name": "Punic\\Data::merge", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Exception.html", "name": "Punic\\Exception", "doc": "&quot;An exception raised by and associated to Punic.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception.html#method___construct", "name": "Punic\\Exception::__construct", "doc": "&quot;Initializes the instance.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/BadArgumentType.html", "name": "Punic\\Exception\\BadArgumentType", "doc": "&quot;An exception raised when a function meets an argument of an unsupported type.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\BadArgumentType", "fromLink": "Punic/Exception/BadArgumentType.html", "link": "Punic/Exception/BadArgumentType.html#method___construct", "name": "Punic\\Exception\\BadArgumentType::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\BadArgumentType", "fromLink": "Punic/Exception/BadArgumentType.html", "link": "Punic/Exception/BadArgumentType.html#method_getArgumentValue", "name": "Punic\\Exception\\BadArgumentType::getArgumentValue", "doc": "&quot;Retrieves the value of the invalid argument.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\BadArgumentType", "fromLink": "Punic/Exception/BadArgumentType.html", "link": "Punic/Exception/BadArgumentType.html#method_getDestinationTypeDescription", "name": "Punic\\Exception\\BadArgumentType::getDestinationTypeDescription", "doc": "&quot;Retrieves the destination type (or a list of destination types).&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/BadDataFileContents.html", "name": "Punic\\Exception\\BadDataFileContents", "doc": "&quot;An exception raised when an data file contains malformed data.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\BadDataFileContents", "fromLink": "Punic/Exception/BadDataFileContents.html", "link": "Punic/Exception/BadDataFileContents.html#method___construct", "name": "Punic\\Exception\\BadDataFileContents::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\BadDataFileContents", "fromLink": "Punic/Exception/BadDataFileContents.html", "link": "Punic/Exception/BadDataFileContents.html#method_getDataFilePath", "name": "Punic\\Exception\\BadDataFileContents::getDataFilePath", "doc": "&quot;Retrieves the path to the data file.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\BadDataFileContents", "fromLink": "Punic/Exception/BadDataFileContents.html", "link": "Punic/Exception/BadDataFileContents.html#method_getDataFileContents", "name": "Punic\\Exception\\BadDataFileContents::getDataFileContents", "doc": "&quot;Retrieves the malformed contents of the file.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/DataFileNotFound.html", "name": "Punic\\Exception\\DataFileNotFound", "doc": "&quot;An exception raised when an data file has not been found.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\DataFileNotFound", "fromLink": "Punic/Exception/DataFileNotFound.html", "link": "Punic/Exception/DataFileNotFound.html#method___construct", "name": "Punic\\Exception\\DataFileNotFound::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\DataFileNotFound", "fromLink": "Punic/Exception/DataFileNotFound.html", "link": "Punic/Exception/DataFileNotFound.html#method_getIdentifier", "name": "Punic\\Exception\\DataFileNotFound::getIdentifier", "doc": "&quot;Retrieves the bad data file identifier.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\DataFileNotFound", "fromLink": "Punic/Exception/DataFileNotFound.html", "link": "Punic/Exception/DataFileNotFound.html#method_getLocale", "name": "Punic\\Exception\\DataFileNotFound::getLocale", "doc": "&quot;Retrieves the preferred locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\DataFileNotFound", "fromLink": "Punic/Exception/DataFileNotFound.html", "link": "Punic/Exception/DataFileNotFound.html#method_getFallbackLocale", "name": "Punic\\Exception\\DataFileNotFound::getFallbackLocale", "doc": "&quot;Retrieves the fallback locale.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/DataFileNotReadable.html", "name": "Punic\\Exception\\DataFileNotReadable", "doc": "&quot;An exception raised when an data file was not read.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\DataFileNotReadable", "fromLink": "Punic/Exception/DataFileNotReadable.html", "link": "Punic/Exception/DataFileNotReadable.html#method___construct", "name": "Punic\\Exception\\DataFileNotReadable::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\DataFileNotReadable", "fromLink": "Punic/Exception/DataFileNotReadable.html", "link": "Punic/Exception/DataFileNotReadable.html#method_getDataFilePath", "name": "Punic\\Exception\\DataFileNotReadable::getDataFilePath", "doc": "&quot;Retrieves the path to the unreadable file.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/DataFolderNotFound.html", "name": "Punic\\Exception\\DataFolderNotFound", "doc": "&quot;An exception raised when an data folder has not been found.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\DataFolderNotFound", "fromLink": "Punic/Exception/DataFolderNotFound.html", "link": "Punic/Exception/DataFolderNotFound.html#method___construct", "name": "Punic\\Exception\\DataFolderNotFound::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\DataFolderNotFound", "fromLink": "Punic/Exception/DataFolderNotFound.html", "link": "Punic/Exception/DataFolderNotFound.html#method_getLocale", "name": "Punic\\Exception\\DataFolderNotFound::getLocale", "doc": "&quot;Retrieves the preferred locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\DataFolderNotFound", "fromLink": "Punic/Exception/DataFolderNotFound.html", "link": "Punic/Exception/DataFolderNotFound.html#method_getFallbackLocale", "name": "Punic\\Exception\\DataFolderNotFound::getFallbackLocale", "doc": "&quot;Retrieves the fallback locale.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/InvalidDataFile.html", "name": "Punic\\Exception\\InvalidDataFile", "doc": "&quot;An exception raised when an data file has been hit.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\InvalidDataFile", "fromLink": "Punic/Exception/InvalidDataFile.html", "link": "Punic/Exception/InvalidDataFile.html#method___construct", "name": "Punic\\Exception\\InvalidDataFile::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\InvalidDataFile", "fromLink": "Punic/Exception/InvalidDataFile.html", "link": "Punic/Exception/InvalidDataFile.html#method_getIdentifier", "name": "Punic\\Exception\\InvalidDataFile::getIdentifier", "doc": "&quot;Retrieves the bad data file identifier.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/InvalidLocale.html", "name": "Punic\\Exception\\InvalidLocale", "doc": "&quot;An exception raised when an invalid locale specification has been hit.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\InvalidLocale", "fromLink": "Punic/Exception/InvalidLocale.html", "link": "Punic/Exception/InvalidLocale.html#method___construct", "name": "Punic\\Exception\\InvalidLocale::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\InvalidLocale", "fromLink": "Punic/Exception/InvalidLocale.html", "link": "Punic/Exception/InvalidLocale.html#method_getLocale", "name": "Punic\\Exception\\InvalidLocale::getLocale", "doc": "&quot;Retrieves the bad locale.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/InvalidOverride.html", "name": "Punic\\Exception\\InvalidOverride", "doc": "&quot;An exception raised when an invalid data override is provided.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\InvalidOverride", "fromLink": "Punic/Exception/InvalidOverride.html", "link": "Punic/Exception/InvalidOverride.html#method___construct", "name": "Punic\\Exception\\InvalidOverride::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\InvalidOverride", "fromLink": "Punic/Exception/InvalidOverride.html", "link": "Punic/Exception/InvalidOverride.html#method_dataToString", "name": "Punic\\Exception\\InvalidOverride::dataToString", "doc": "&quot;Convert override data to a string.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/NotImplemented.html", "name": "Punic\\Exception\\NotImplemented", "doc": "&quot;An exception raised when a function meets an argument of an unsupported type.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\NotImplemented", "fromLink": "Punic/Exception/NotImplemented.html", "link": "Punic/Exception/NotImplemented.html#method___construct", "name": "Punic\\Exception\\NotImplemented::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\NotImplemented", "fromLink": "Punic/Exception/NotImplemented.html", "link": "Punic/Exception/NotImplemented.html#method_getFunction", "name": "Punic\\Exception\\NotImplemented::getFunction", "doc": "&quot;Retrieves the name of the not implemented function\/method.&quot;"},
            
            {"type": "Class", "fromName": "Punic\\Exception", "fromLink": "Punic/Exception.html", "link": "Punic/Exception/ValueNotInList.html", "name": "Punic\\Exception\\ValueNotInList", "doc": "&quot;An exception raised when a function meets an argument of an unsupported type.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Exception\\ValueNotInList", "fromLink": "Punic/Exception/ValueNotInList.html", "link": "Punic/Exception/ValueNotInList.html#method___construct", "name": "Punic\\Exception\\ValueNotInList::__construct", "doc": "&quot;Initializes the instance.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\ValueNotInList", "fromLink": "Punic/Exception/ValueNotInList.html", "link": "Punic/Exception/ValueNotInList.html#method_getValue", "name": "Punic\\Exception\\ValueNotInList::getValue", "doc": "&quot;Retrieves the invalid value.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Exception\\ValueNotInList", "fromLink": "Punic/Exception/ValueNotInList.html", "link": "Punic/Exception/ValueNotInList.html#method_getAllowedValues", "name": "Punic\\Exception\\ValueNotInList::getAllowedValues", "doc": "&quot;Retrieves the list of valid values.&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Language.html", "name": "Punic\\Language", "doc": "&quot;Language-related stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Language", "fromLink": "Punic/Language.html", "link": "Punic/Language.html#method_getAll", "name": "Punic\\Language::getAll", "doc": "&quot;Return all the languages.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Language", "fromLink": "Punic/Language.html", "link": "Punic/Language.html#method_getName", "name": "Punic\\Language::getName", "doc": "&quot;Retrieve the name of a language.&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Misc.html", "name": "Punic\\Misc", "doc": "&quot;Various helper stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_joinAnd", "name": "Punic\\Misc::joinAnd", "doc": "&quot;Concatenates a list of items returning a localized string using \&quot;and\&quot; as seperator.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_joinOr", "name": "Punic\\Misc::joinOr", "doc": "&quot;Concatenates a list of items returning a localized string using \&quot;or\&quot; as seperator.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_joinUnits", "name": "Punic\\Misc::joinUnits", "doc": "&quot;Concatenates a list of unit items returning a localized string.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_fixCase", "name": "Punic\\Misc::fixCase", "doc": "&quot;Fix the case of a string.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_getBrowserLocales", "name": "Punic\\Misc::getBrowserLocales", "doc": "&quot;Parse the browser HTTP_ACCEPT_LANGUAGE header and return the found locales, sorted in descending order by the quality values.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_parseHttpAcceptLanguage", "name": "Punic\\Misc::parseHttpAcceptLanguage", "doc": "&quot;Parse the value of an HTTP_ACCEPT_LANGUAGE header and return the found locales, sorted in descending order by the quality values.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_getCharacterOrder", "name": "Punic\\Misc::getCharacterOrder", "doc": "&quot;Retrieve the character order (right-to-left or left-to-right).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_getLineOrder", "name": "Punic\\Misc::getLineOrder", "doc": "&quot;Retrieve the line order (top-to-bottom or bottom-to-top).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_join", "name": "Punic\\Misc::join", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Misc", "fromLink": "Punic/Misc.html", "link": "Punic/Misc.html#method_joinInternal", "name": "Punic\\Misc::joinInternal", "doc": "&quot;Concatenates a list of items returning a localized string.&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Number.html", "name": "Punic\\Number", "doc": "&quot;Numbers helpers.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Number", "fromLink": "Punic/Number.html", "link": "Punic/Number.html#method_isNumeric", "name": "Punic\\Number::isNumeric", "doc": "&quot;Check if a variable contains a valid number for the specified locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Number", "fromLink": "Punic/Number.html", "link": "Punic/Number.html#method_isInteger", "name": "Punic\\Number::isInteger", "doc": "&quot;Check if a variable contains a valid integer number for the specified locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Number", "fromLink": "Punic/Number.html", "link": "Punic/Number.html#method_format", "name": "Punic\\Number::format", "doc": "&quot;Localize a number representation (for instance, converts 1234.5 to &#039;1,234.5&#039; in case of English and to &#039;1.234,5&#039; in case of Italian).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Number", "fromLink": "Punic/Number.html", "link": "Punic/Number.html#method_unformat", "name": "Punic\\Number::unformat", "doc": "&quot;Convert a localized representation of a number to a number (for instance, converts the string &#039;1,234&#039; to 1234 in case of English and to 1.234 in case of Italian).&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Phone.html", "name": "Punic\\Phone", "doc": "&quot;Numbers helpers.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Phone", "fromLink": "Punic/Phone.html", "link": "Punic/Phone.html#method_getPrefixesForTerritory", "name": "Punic\\Phone::getPrefixesForTerritory", "doc": "&quot;Retrieve the list of the country calling codes for a specific country.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Phone", "fromLink": "Punic/Phone.html", "link": "Punic/Phone.html#method_getTerritoriesForPrefix", "name": "Punic\\Phone::getTerritoriesForPrefix", "doc": "&quot;Retrieve the list of territory codes for a specific prefix.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Phone", "fromLink": "Punic/Phone.html", "link": "Punic/Phone.html#method_getMaxPrefixLength", "name": "Punic\\Phone::getMaxPrefixLength", "doc": "&quot;Retrieve the max length of the country calling codes.&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Plural.html", "name": "Punic\\Plural", "doc": "&quot;Plural helper stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Plural", "fromLink": "Punic/Plural.html", "link": "Punic/Plural.html#method_getRules", "name": "Punic\\Plural::getRules", "doc": "&quot;Return the list of applicable plural rule for a locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Plural", "fromLink": "Punic/Plural.html", "link": "Punic/Plural.html#method_getRule", "name": "Punic\\Plural::getRule", "doc": "&quot;Return the plural rule (&#039;zero&#039;, &#039;one&#039;, &#039;two&#039;, &#039;few&#039;, &#039;many&#039; or &#039;other&#039;) for a number and a locale.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Plural", "fromLink": "Punic/Plural.html", "link": "Punic/Plural.html#method_inRange", "name": "Punic\\Plural::inRange", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Territory.html", "name": "Punic\\Territory", "doc": "&quot;Territory-related stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getName", "name": "Punic\\Territory::getName", "doc": "&quot;Retrieve the name of a territory\/subdivision (country, continent, .&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getCode", "name": "Punic\\Territory::getCode", "doc": "&quot;Retrieve the code of a territory in a different coding system.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getByCode", "name": "Punic\\Territory::getByCode", "doc": "&quot;Retrieve the territory code given its code in a different coding system.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getContinents", "name": "Punic\\Territory::getContinents", "doc": "&quot;Return the list of continents in the form of an array with key=ID, value=name.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getCountries", "name": "Punic\\Territory::getCountries", "doc": "&quot;Return the list of countries in the form of an array with key=ID, value=name.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getContinentsAndCountries", "name": "Punic\\Territory::getContinentsAndCountries", "doc": "&quot;Return a list of continents and relative countries. The resulting array is in the following form (JSON representation):\n```json\n{\n    \&quot;002\&quot;: {\n        \&quot;name\&quot;: \&quot;Africa\&quot;,\n        \&quot;children\&quot;: {\n            \&quot;DZ\&quot;: {\&quot;name\&quot;: \&quot;Algeria\&quot;},\n            \&quot;AO\&quot;: {\&quot;name\&quot;: \&quot;Angola\&quot;},\n            .&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getList", "name": "Punic\\Territory::getList", "doc": "&quot;Return a list of some specified territory\/subdivision, structured or not.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getTerritoriesWithInfo", "name": "Punic\\Territory::getTerritoriesWithInfo", "doc": "&quot;Return a list of territory identifiers for which we have some info (languages, population, literacy level, Gross Domestic Product).&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getLanguages", "name": "Punic\\Territory::getLanguages", "doc": "&quot;Return the list of languages spoken in a territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getPopulation", "name": "Punic\\Territory::getPopulation", "doc": "&quot;Return the population of a specific territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getLiteracyLevel", "name": "Punic\\Territory::getLiteracyLevel", "doc": "&quot;Return the literacy level for a specific territory, in %.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getGrossDomesticProduct", "name": "Punic\\Territory::getGrossDomesticProduct", "doc": "&quot;Return the GDP (Gross Domestic Product) for a specific territory, in US$.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getTerritoriesForLanguage", "name": "Punic\\Territory::getTerritoriesForLanguage", "doc": "&quot;Return a list of territory IDs where a specific language is spoken, sorted by the total number of people speaking that language.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getParentTerritoryCode", "name": "Punic\\Territory::getParentTerritoryCode", "doc": "&quot;Return the code of the territory\/subdivision that contains a territory\/subdivision.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getChildTerritoryCodes", "name": "Punic\\Territory::getChildTerritoryCodes", "doc": "&quot;Retrieve the child territories\/subdivisions of a parent territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getTerritoryInfo", "name": "Punic\\Territory::getTerritoryInfo", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_getStructure", "name": "Punic\\Territory::getStructure", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_fillStructure", "name": "Punic\\Territory::fillStructure", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_finalizeWithNames", "name": "Punic\\Territory::finalizeWithNames", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_filterStructure", "name": "Punic\\Territory::filterStructure", "doc": "&quot;&quot;"},
                    {"type": "Method", "fromName": "Punic\\Territory", "fromLink": "Punic/Territory.html", "link": "Punic/Territory.html#method_sort", "name": "Punic\\Territory::sort", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "Punic", "fromLink": "Punic.html", "link": "Punic/Unit.html", "name": "Punic\\Unit", "doc": "&quot;Units helper stuff.&quot;"},
                                                        {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_getAvailableUnits", "name": "Punic\\Unit::getAvailableUnits", "doc": "&quot;Get the list of all the available units.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_getName", "name": "Punic\\Unit::getName", "doc": "&quot;Get the localized name of a unit.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_format", "name": "Punic\\Unit::format", "doc": "&quot;Format a unit string.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_getMeasurementSystems", "name": "Punic\\Unit::getMeasurementSystems", "doc": "&quot;Retrieve the measurement systems and their localized names.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_getMeasurementSystemFor", "name": "Punic\\Unit::getMeasurementSystemFor", "doc": "&quot;Retrieve the measurement system for a specific territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_getCountriesWithMeasurementSystem", "name": "Punic\\Unit::getCountriesWithMeasurementSystem", "doc": "&quot;Returns the list of countries that use a specific measurement system.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_getPaperSizeFor", "name": "Punic\\Unit::getPaperSizeFor", "doc": "&quot;Retrieve the standard paper size for a specific territory.&quot;"},
                    {"type": "Method", "fromName": "Punic\\Unit", "fromLink": "Punic/Unit.html", "link": "Punic/Unit.html#method_getCountriesWithPaperSize", "name": "Punic\\Unit::getCountriesWithPaperSize", "doc": "&quot;Returns the list of countries that use a specific paper size by default.&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


