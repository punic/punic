<?php
namespace Punic;

class Calendar
{
    /**
     * Convert a date/time representation to a \DateTime instance
     * @param mixed $value A unix timestamp, a \DateTime instance or a string accepted by strtotime
     * @param string|\DateTimeZone $toTimezone The timezone to set; leave empty to use the default timezone (or the timezone associated to $value if it's already a \DateTime)
     * @return \DateTime|null Returns null if $value is empty, a \DateTime instance otherwise
     * @throws \Exception Throws an exception if $value is not empty and can't be converted to a \DateTime instance
     * @link http://php.net/manual/datetime.formats.php
     */
    public static function toDateTime($value, $toTimezone = '')
    {
        $result = null;
        if ((!empty($value)) || ($value === 0) || ($value === '0')) {
            if (is_int($value) || is_float($value)) {
                $result = new \DateTime();
                $result->setTimestamp($value);
            } elseif ($value instanceof \DateTime) {
                $result = clone $value;
            } elseif (is_string($value)) {
                if (is_numeric($value)) {
                    $result = new \DateTime();
                    $result->setTimestamp($value);
                } else {
                    $result = new \DateTime($value);
                }
            } else {
                throw new \Exception("Can't convert a variable of kind " . gettype($value) . " to a \\DateTime instance");
            }
            if ($result) {
                if (!empty($toTimezone)) {
                    if (is_string($toTimezone)) {
                        $result->setTimezone(new \DateTimeZone($toTimezone));
                    } elseif (is_a($toTimezone, '\DateTimeZone')) {
                        $result->setTimezone($toTimezone);
                    } else {
                        throw new \Exception("Can't convert a variable of kind " . gettype($toTimezone) . " to a \\DateTimeZone instance");
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Converts a format string from PHP's date format to ISO format
     * Remember that Zend Date always returns localized string, so a month name which returns the english
     * month in php's date() will return the translated month name with this function... use 'en' as locale
     * if you are in need of the original english names
     *
     * The conversion has the following restrictions:
     * 'a', 'A' - Meridiem is not explicit upper/lowercase, you have to upper/lowercase the translated value yourself
     *
     * @param string $format Format string in PHP's date format
     * @return string Format string in ISO format
     */
    public static function convertPhpToIsoFormat($format)
    {
        static $cache = array();
        static $convert = array(
            'd' => 'dd'  , 'D' => 'EE'  , 'j' => 'd'   , 'l' => 'EEEE',
            'N' => 'eee' , 'S' => 'SS'  , 'w' => 'e'   , 'z' => 'D'   ,
            'W' => 'ww'  , 'F' => 'MMMM', 'm' => 'MM'  , 'M' => 'MMM' ,
            'n' => 'M'   , 't' => 'ddd' , 'L' => 'l'   , 'o' => 'YYYY',
            'Y' => 'yyyy', 'y' => 'yy'  , 'a' => 'a'   , 'A' => 'a'   ,
            'B' => 'B'   , 'g' => 'h'   , 'G' => 'H'   , 'h' => 'hh'  ,
            'H' => 'HH'  , 'i' => 'mm'  , 's' => 'ss'  , 'e' => 'zzzz',
            'I' => 'I'   , 'O' => 'Z'   , 'P' => 'ZZZZ', 'T' => 'z'   ,
            'Z' => 'X'   , 'c' => 'yyyy-MM-ddTHH:mm:ssZZZZ', 'r' => 'r',
            'U' => 'U',
        );
        if (!is_string($format)) {
            return null;
        }
        if (!array_key_exists($format, $cache)) {
            $escaped = false;
            $inEscapedString = false;
            $converted = array();
            foreach (str_split($format) as $char) {
                if (!$escaped && $char == '\\') {
                    // Next char will be escaped: let's remember it
                    $escaped = true;
                } elseif ($escaped) {
                    if (!$inEscapedString) {
                        // First escaped string: start the quoted chunk
                        $converted[] = "'";
                        $inEscapedString = true;
                    }
                    // Since the previous char was a \ and we are in the quoted
                    // chunk, let's simply add $char as it is
                    $converted[] = $char;
                    $escaped = false;
                } elseif ($char == "'") {
                    // Single quotes need to be escaped like this
                    $converted[] = "''";
                } else {
                    if ($inEscapedString) {
                        // Close the single-quoted chunk
                        $converted[] = "'";
                        $inEscapedString = false;
                    }
                    // Convert the unescaped char if needed
                    if (isset($convert[$char])) {
                        $converted[] = $convert[$char];
                    } else {
                        $converted[] = $char;
                    }
                }
            }
            $cache[$format] = implode($converted);
        }

        return $cache[$format];
    }

    /**
     * Get the name of an era
     * @param mixed $value A year or a \DateTime instance
     * @param string $width = 'abbreviated' The format name; it can be 'wide' (eg 'Before Christ'), 'abbreviated' (eg 'BC') or 'narrow' (eg 'B')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the name of the era otherwise
     * @throws \Exception Throws an exception in case of problems
     */
    public static function getEraName($value, $width = 'abbreviated', $locale = '')
    {
        $result = '';
        if ((!empty($value)) || ($value === 0) || ($value === '0')) {
            $year = null;
            if (is_int($value)) {
                $year = $value;
            } elseif (is_float($value)) {
                $year = intval($value);
            } elseif (is_string($value)) {
                if (is_numeric($value)) {
                    $year = intval($value);
                }
            } elseif (is_a($value, '\DateTime')) {
                $year = intval($value->format('Y'));
            }
            if (is_null($year)) {
                throw new \Exception("Can't convert a variable of kind " . gettype($value) . " to a year number");
            }
            $data = \Punic\Data::get('calendar', $locale);
            $data = $data['eras'];
            if (!array_key_exists($width, $data)) {
                throw new \Exception("Invalid format: $width\nAvailable formats: " . implode(', ', array_keys($data)));
            }
            $result = $data[$width][($year < 0) ? '0' : '1'];
        }

        return $result;
    }

    /**
     * Get the name of a month
     * @param mixed $value A month number (1-12) or a \DateTime instance
     * @param string $width = 'wide' The format name; it can be 'wide' (eg 'January'), 'abbreviated' (eg 'Jan') or 'narrow' (eg 'J')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @param bool $standAlone = false Set to true to return the form used independently (such as in calendar header)
     * @return string Returns an empty string if $value is empty, the name of the month name otherwise
     * @throws \Exception Throws an exception in case of problems
     */
    public static function getMonthName($value, $width = 'wide', $locale = '', $standAlone = false)
    {
        $result = '';
        if ((!empty($value)) || ($value === 0) || ($value === '0')) {
            $month = null;
            if (is_int($value)) {
                $month = $value;
            } elseif (is_float($value)) {
                $month = intval($value);
            } elseif (is_string($value)) {
                if (is_numeric($value)) {
                    $month = intval($value);
                }
            } elseif (is_a($value, '\DateTime')) {
                $month = intval($value->format('n'));
            }
            if (is_null($month)) {
                throw new \Exception("Can't convert a variable of kind " . gettype($value) . " to a month number");
            }
            if (($month < 1) || ($month > 12)) {
                throw new \Exception("Invalid month number ($month)");
            }
            $data = \Punic\Data::get('calendar', $locale);
            $data = $data['months'][$standAlone ? 'stand-alone' : 'format'];
            if (!array_key_exists($width, $data)) {
                throw new \Exception("Invalid format: $width\nAvailable formats: " . implode(', ', array_keys($data)));
            }
            $result = $data[$width][$month];
        }

        return $result;
    }

    /**
     * Get the name of a weekday
     * @param mixed $value A weekday number (from 0-Sunday to 6-Saturnday) or a \DateTime instance
     * @param string $width = 'wide' The format name; it can be 'wide' (eg 'Sunday'), 'abbreviated' (eg 'Sun'), 'short' (eg 'Su') or 'narrow' (eg 'S')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @param bool $standAlone = false Set to true to return the form used independently (such as in calendar header)
     * @return string Returns an empty string if $value is empty, the name of the weekday name otherwise
     * @throws \Exception Throws an exception in case of problems
     */
    public static function getWeekdayName($value, $width = 'wide', $locale = '', $standAlone = false)
    {
        static $dictionary = array('sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat');
        $result = '';
        if ((!empty($value)) || ($value === 0) || ($value === '0')) {
            $weekday = null;
            if (is_int($value)) {
                $weekday = $value;
            } elseif (is_float($value)) {
                $weekday = intval($value);
            } elseif (is_string($value)) {
                if (is_numeric($value)) {
                    $weekday = intval($value);
                }
            } elseif (is_a($value, '\DateTime')) {
                $weekday = intval($value->format('w'));
            }
            if (is_null($weekday)) {
                throw new \Exception("Can't convert a variable of kind " . gettype($value) . " to a weekday number");
            }
            if (($weekday < 0) || ($weekday > 6)) {
                throw new \Exception("Invalid weekday number ($weekday)");
            }
            $weekday = $dictionary[$weekday];
            $data = \Punic\Data::get('calendar', $locale);
            $data = $data['days'][$standAlone ? 'stand-alone' : 'format'];
            if (!array_key_exists($width, $data)) {
                throw new \Exception("Invalid format: $width\nAvailable formats: " . implode(', ', array_keys($data)));
            }
            $result = $data[$width][$weekday];
        }

        return $result;
    }

    /**
     * Get the name of a quarter
     * @param mixed $value A quarter number (from 1 to 4) or a \DateTime instance
     * @param string $width = 'wide' The format name; it can be 'wide' (eg '1st quarter'), 'abbreviated' (eg 'Q1') or 'narrow' (eg '1')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @param bool $standAlone = false Set to true to return the form used independently (such as in calendar header)
     * @return string Returns an empty string if $value is empty, the name of the quarter name otherwise
     * @throws \Exception Throws an exception in case of problems
     */
    public static function getQuarterName($value, $width = 'wide', $locale = '', $standAlone = false)
    {
        $result = '';
        if ((!empty($value)) || ($value === 0) || ($value === '0')) {
            $quarter = null;
            if (is_int($value)) {
                $quarter = $value;
            } elseif (is_float($value)) {
                $quarter = intval($value);
            } elseif (is_string($value)) {
                if (is_numeric($value)) {
                    $quarter = intval($value);
                }
            } elseif (is_a($value, '\DateTime')) {
                $quarter = 1 + intval(floor((intval($value->format('n')) - 1) / 3));
            }
            if (is_null($quarter)) {
                throw new \Exception("Can't convert a variable of kind " . gettype($value) . " to a quarter number");
            }
            if (($quarter < 1) || ($quarter > 4)) {
                throw new \Exception("Invalid quarter number ($quarter)");
            }
            $data = \Punic\Data::get('calendar', $locale);
            $data = $data['quarters'][$standAlone ? 'stand-alone' : 'format'];
            if (!array_key_exists($width, $data)) {
                throw new \Exception("Invalid format: $width\nAvailable formats: " . implode(', ', array_keys($data)));
            }
            $result = $data[$width][$quarter];
        }

        return $result;
    }

    /**
     * Get the name of a day period (AM/PM)
     * @param mixed $value An hour (from 0 to 23), a standard period name('am' or 'pm', lower or upper case) or a \DateTime instance
     * @param string $width = 'wide' The format name; it can be 'wide' (eg 'AM'), 'abbreviated' (eg 'AM') or 'narrow' (eg 'a')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @param bool $standAlone = false Set to true to return the form used independently (such as in calendar header)
     * @return string Returns an empty string if $value is empty, the name of the period name otherwise
     * @throws \Exception Throws an exception in case of problems
     */
    public static function getDayperiodName($value, $width = 'wide', $locale = '', $standAlone = false)
    {
        static $dictionary = array('am', 'pm');
        $result = '';
        if ((!empty($value)) || ($value === 0) || ($value === '0')) {
            $dayperiod = null;
            $hours = null;
            if (is_int($value)) {
                $hours = $value;
            } elseif (is_float($value)) {
                $hours = intval($value);
            } elseif (is_string($value)) {
                if (is_numeric($value)) {
                    $hours = intval($value);
                } else {
                    $s = strtolower($value);
                    if (in_array($s, $dictionary, true)) {
                        $dayperiod = $s;
                    }
                }
            } elseif (is_a($value, '\DateTime')) {
                $dayperiod = $value->format('a');
            }
            if ((!is_null($hours)) && ($hours >= 0) && ($hours <= 23)) {
                $dayperiod = ($hours < 12) ? 'am' : 'pm';
            }
            if (is_null($dayperiod)) {
                throw new \Exception("Can't convert a variable of kind " . gettype($value) . " to a dayperiod identifier");
            }
            $data = \Punic\Data::get('calendar', $locale);
            $data = $data['dayPeriods'][$standAlone ? 'stand-alone' : 'format'];
            if (!array_key_exists($width, $data)) {
                throw new \Exception("Invalid format: $width\nAvailable formats: " . implode(', ', array_keys($data)));
            }
            $result = $data[$width][$dayperiod];
        }

        return $result;
    }

    /**
     * Returns the localized name of a timezone
     * @param string|\DateTime|\DateTimeZone $value The php name of a timezone, or a \DateTime instance or a \DateTimeZone instance
     * @param string $width = 'long' The format name; it can be 'long' (eg 'Greenwich Mean Time') or 'short' (eg 'GMT')
     * @param string $kind = '' Set to 'daylight' to retrieve the daylight saving time name, set to 'standard' to retrieve the standard time, set to 'generic' to retrieve the generic name, set to '' to determine automatically the dst (if $value is \DateTime) or the generic (otherwise)
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if the timezone has not been found (maybe we don't have the data in the specified $width), the timezone name otherwise
     */
    public static function getTimezoneNameNoLocationSpecific($value, $width = 'long', $kind = '',  $locale = '')
    {
        $result = '';
        if (!empty($value)) {
            $phpName = '';
            $date = '';
            if (is_string($value)) {
                $phpName = $value;
            } elseif (is_a($value, '\\DateTime')) {
                $phpName = $value->getTimezone()->getName();
                $date = $value->format('Y-m-d H:i');
                if (empty($kind)) {
                    if (intval($value->format('I')) === 1) {
                        $kind = 'daylight';
                    } else {
                        $kind = 'standard';
                    }
                }
            } elseif (is_a($value, '\\DateTimeZone')) {
                $phpName = $value->getName();
            }
            if (strlen($phpName)) {
                $chunks = array_merge(array('metazoneInfo'), explode('/', $phpName));
                $tzInfo = \Punic\Data::getGeneric('metaZones');
                foreach ($chunks as $chunk) {
                    if (array_key_exists($chunk, $tzInfo)) {
                        $tzInfo = $tzInfo[$chunk];
                    } else {
                        $tzInfo = null;
                        break;
                    }
                }
                $isoName = '';
                if (is_array($tzInfo)) {
                    foreach ($tzInfo as $tz) {
                        if (is_array($tz) && array_key_exists('mzone', $tz)) {
                            if (strlen($date)) {
                                if (array_key_exists('from', $tz) && (strcmp($date, $tz['from']) < 0)) {
                                    continue;
                                }
                                if (array_key_exists('to', $tz) && (strcmp($date, $tz['to']) >= 0)) {
                                    continue;
                                }
                            }
                            $isoName = $tz['mzone'];
                            break;
                        }
                    }
                }
                if (!strlen($isoName)) {
                    $isoName = $phpName;
                }
                if (strlen($isoName)) {
                    $data = \Punic\Data::get('timeZoneNames', $locale);
                    if (array_key_exists('metazone', $data)) {
                        $data = $data['metazone'];
                        if (array_key_exists($isoName, $data)) {
                            $data = $data[$isoName];
                            if (array_key_exists($width, $data)) {
                                $data = $data[$width];
                                $lookFor = array();
                                if (!empty($kind)) {
                                    $lookFor[] = $kind;
                                }
                                $lookFor[] = 'generic';
                                $lookFor[] = 'standard';
                                $lookFor[] = 'daylight';
                                foreach ($lookFor as $lf) {
                                    if (array_key_exists($lf, $data)) {
                                        $result = $data[$lf];
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }

    /** @todo I can't find data for this */
    public static function getTimezoneNameLocationSpecific()
    {
        return '';
    }

    /**
     * Returns the localized name of an exemplar city for a specific timezone
     * @param string|\DateTime|\DateTimeZone $value The php name of a timezone, or a \DateTime instance or a \DateTimeZone instance
     * @param bool $returnUnknownIfNotFound true If the exemplar city is not found, shall we return the translation of 'Unknown City'?
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if the exemplar city hasn't been found and $returnUnknownIfNotFound is false
     */
    public static function getTimezoneExemplarCity($value, $returnUnknownIfNotFound = true, $locale = '')
    {
        $result = '';
        $locale = empty($locale) ? \Punic\Data::getDefaultLocale() : $locale;
        if (!empty($value)) {
            $phpName = '';
            $date = '';
            if (is_string($value)) {
                $phpName = $value;
            } elseif (is_a($value, '\\DateTime')) {
                $phpName = $value->getTimezone()->getName();
            } elseif (is_a($value, '\\DateTimeZone')) {
                $phpName = $value->getName();
            }
            if (strlen($phpName)) {
                $chunks = array_merge(array('zone'), explode('/', $phpName));
                $data = \Punic\Data::get('timeZoneNames', $locale);
                foreach ($chunks as $chunk) {
                    if (array_key_exists($chunk, $data)) {
                        $data = $data[$chunk];
                    } else {
                        $data = null;
                    }
                    if (!is_array($data)) {
                        break;
                    }
                }
                if (is_array($data) && array_key_exists('exemplarCity', $data)) {
                    $result = $data['exemplarCity'];
                }
            }
        }
        if ((!strlen($result)) && $returnUnknownIfNotFound) {
            $result = static::getTimezoneExemplarCity('Etc/Unknown', false, $locale);
            if (!strlen($result)) {
                $result = 'Unknown City';
            }
        }

        return $result;
    }

    /**
     * Returns true if a locale has a 12-hour clock, false if 24-hour clock
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return bool
     * @throws \Exception Throws an exception in case of problems
     */
    public static function has12HoursClock($locale = '')
    {
        static $cache = array();
        $locale = empty($locale) ? \Punic\Data::getDefaultLocale() : $locale;
        if (!array_key_exists($locale, $cache)) {
            $format = static::getTimeFormat('short', $locale);
            $format = str_replace("''", '', $format);
            $cache[$locale] = (strpos($format, 'a') === false) ? false : true;
        }

        return $cache[$locale];
    }

    /**
     * Retrieve the first weekday for a specific locale (from 0-Sunday to 6-Saturnday)
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return int Returns a number from 0 (Sunday) to 7 (Saturnday)
     */
    public static function getFirstWeekday($locale = '')
    {
        static $cache = array();
        $locale = empty($locale) ? \Punic\Data::getDefaultLocale() : $locale;
        if (!array_key_exists($locale, $cache)) {
            $data = \Punic\Data::getGeneric('weekData');
            $result = \Punic\Data::getTerritoryNode($data['firstDay'], $locale);
            if (!is_int($result)) {
                $result = 0;
            }
            $cache[$locale] = $result;
        }

        return $cache[$locale];
    }

    /**
     * Get the ISO format for a date
     * @param string $width The format name; it can be 'full' (eg 'EEEE, MMMM d, y' - 'Wednesday, August 20, 2014'), 'long' (eg 'MMMM d, y' - 'August 20, 2014'), 'medium' (eg 'MMM d, y' - 'August 20, 2014') or 'short' (eg 'M/d/yy' - '8/20/14')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns the requested ISO format
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function getDateFormat($width, $locale = '')
    {
        $data = \Punic\Data::get('calendar', $locale);
        $data = $data['dateFormats'];
        if (!array_key_exists($width, $data)) {
            throw new \Exception("Invalid format: $width\nAvailable formats: " . implode(', ', array_keys($data)));
        }

        return $data[$width];
    }

    /**
     * Get the ISO format for a time
     * @param string $width The format name; it can be 'full' (eg 'h:mm:ss a zzzz' - '11:42:13 AM GMT+2:00'), 'long' (eg 'h:mm:ss a z' - '11:42:13 AM GMT+2:00'), 'medium' (eg 'h:mm:ss a' - '11:42:13 AM') or 'short' (eg 'h:mm a' - '11:42 AM')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns the requested ISO format
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function getTimeFormat($width, $locale = '')
    {
        $data = \Punic\Data::get('calendar', $locale);
        $data = $data['timeFormats'];
        if (!array_key_exists($width, $data)) {
            throw new \Exception("Invalid format: $width\nAvailable formats: " . implode(', ', array_keys($data)));
        }

        return $data[$width];
    }

    /**
     * Get the ISO format for a date/time
     * @param string $width The format name; it can be 'full', 'long', 'medium', 'short' or a combination for date+time like 'full|short' or a combination for format+date+time like 'full|full|short'
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns the requested ISO format
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function getDatetimeFormat($width, $locale = '')
    {
        $chunks = explode('|', $width);
        switch (count($chunks)) {
            case 1:
                $timeWidth = $dateWidth = $wholeWidth = $width;
                break;
            case 2:
                $sortedChunks = $chunks;
                usort($sortedChunks, function ($a, $b) {
                    $cmp = 0;
                    if ($a !== $b) {
                        foreach (array('full', 'long', 'medium', 'short') as $w) {
                            if ($a === $w) {
                                $cmp = -1;
                                break;
                            }
                            if ($b === $w) {
                                $cmp = 1;
                                break;
                            }
                        }
                    }

                    return $cmp;
                });
                $wholeWidth = $sortedChunks[0];
                $dateWidth = $chunks[0];
                $timeWidth = $chunks[1];
                break;
            case 3:
                $wholeWidth = $chunks[0];
                $dateWidth = $chunks[1];
                $timeWidth = $chunks[2];
                break;
            default:
                throw new \Exception("Invalid format: $width");
        }
        $data = \Punic\Data::get('calendar', $locale);
        $data = $data['dateTimeFormats'];
        if (!array_key_exists($wholeWidth, $data)) {
            throw new \Exception("Invalid format: $wholeWidth\nAvailable formats: " . implode(', ', array_keys($wholeWidth)));
        }

        return sprintf(
            $data[$wholeWidth],
            static::getTimeFormat($timeWidth, $locale),
            static::getDateFormat($dateWidth, $locale)
        );
    }

    /**
     * Format a date
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $width The format name; it can be 'full' (eg 'EEEE, MMMM d, y' - 'Wednesday, August 20, 2014'), 'long' (eg 'MMMM d, y' - 'August 20, 2014'), 'medium' (eg 'MMM d, y' - 'August 20, 2014') or 'short' (eg 'M/d/yy' - '8/20/14')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function formatDate($value, $width, $locale = '')
    {
        return static::format(
            $value,
            static::getDateFormat($width, $locale),
            $locale
        );
    }

    /**
     * Format a date (extended version: various date/time representations - see toDateTime())
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $width The format name; it can be 'full' (eg 'EEEE, MMMM d, y' - 'Wednesday, August 20, 2014'), 'long' (eg 'MMMM d, y' - 'August 20, 2014'), 'medium' (eg 'MMM d, y' - 'August 20, 2014') or 'short' (eg 'M/d/yy' - '8/20/14')
     * @param string|\DateTimeZone $toTimezone The timezone to set; leave empty to use the default timezone (or the timezone associated to $value if it's already a \DateTime)
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @see toDateTime()
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function formatDateEx($value, $width, $toTimezone = '', $locale = '')
    {
        return static::formatEx(
            $value,
            static::getDateFormat($width, $locale),
            $toTimezone,
            $locale
        );
    }

    /**
     * Format a time
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $width The format name; it can be 'full' (eg 'h:mm:ss a zzzz' - '11:42:13 AM GMT+2:00'), 'long' (eg 'h:mm:ss a z' - '11:42:13 AM GMT+2:00'), 'medium' (eg 'h:mm:ss a' - '11:42:13 AM') or 'short' (eg 'h:mm a' - '11:42 AM')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function formatTime($value, $width, $locale = '')
    {
        return static::format(
            $value,
            static::getTimeFormat($width, $locale),
            $locale
        );
    }

    /**
     * Format a time (extended version: various date/time representations - see toDateTime())
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $width The format name; it can be 'full' (eg 'h:mm:ss a zzzz' - '11:42:13 AM GMT+2:00'), 'long' (eg 'h:mm:ss a z' - '11:42:13 AM GMT+2:00'), 'medium' (eg 'h:mm:ss a' - '11:42:13 AM') or 'short' (eg 'h:mm a' - '11:42 AM')
     * @param string|\DateTimeZone $toTimezone The timezone to set; leave empty to use the default timezone (or the timezone associated to $value if it's already a \DateTime)
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @see toDateTime()
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function formatTimeEx($value, $width, $toTimezone = '', $locale = '')
    {
        return static::formatEx(
            $value,
            static::getTimeFormat($width, $locale),
            $toTimezone,
            $locale
        );
    }

    /**
     * Format a date/time
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $width The format name; it can be 'full', 'long', 'medium', 'short' or a combination for date+time like 'full|short' or a combination for format+date+time like 'full|full|short'
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function formatDatetime($value, $width, $locale = '')
    {
        return static::format(
            $value,
            static::getDatetimeFormat($width, $locale),
            $locale
        );
    }

    /**
     * Format a date/time (extended version: various date/time representations - see toDateTime())
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $width The format name; it can be 'full', 'long', 'medium', 'short' or a combination for date+time like 'full|short' or a combination for format+date+time like 'full|full|short'
     * @param string|\DateTimeZone $toTimezone The timezone to set; leave empty to use the default timezone (or the timezone associated to $value if it's already a \DateTime)
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @see toDateTime()
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function formatDatetimeEx($value, $width, $toTimezone = '', $locale = '')
    {
        return static::formatEx(
            $value,
            static::getDatetimeFormat($width, $locale),
            $toTimezone,
            $locale
        );
    }

    /**
     * Format a date and/or time
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $format The ISO format that specify how to render the date/time
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function format($value, $format, $locale = '')
    {
        static $decodeCache = array();
        static $decoderFunctions = array(
            'G' => 'decodeEra',
            'y' => 'decodeYear',
            'Y' => 'decodeYearWeekOfYear',
            'u' => 'decodeYearExtended',
            'U' => 'decodeYearCyclicName',
            'r' => 'decodeYearRelatedGregorian',
            'Q' => 'decodeQuarter',
            'q' => 'decodeQuarterAlone',
            'M' => 'decodeMonth',
            'L' => 'decodeMonthAlone',
            'w' => 'decodeWeekOfYear',
            'W' => 'decodeWeekOfMonth',
            'd' => 'decodeDayOfMonth',
            'D' => 'decodeDayOfYear',
            'F' => 'decodeWeekdayInMonth',
            'g' => 'decodeModifiedGiulianDay',
            'E' => 'decodeDayOfWeek',
            'e' => 'decodeDayOfWeekLocal',
            'c' => 'decodeDayOfWeekLocalAlone',
            'a' => 'decodeDayperiod',
            'h' => 'decodeHour12',
            'H' => 'decodeHour24',
            'K' => 'decodeHour12From0',
            'k' => 'decodeHour24From1',
            'm' => 'decodeMinute',
            's' => 'decodeSecond',
            'S' => 'decodeFranctionsOfSeconds',
            'A' => 'decodeMsecInDay',
            'z' => 'decodeTimezoneNoLocationSpecific',
            'Z' => 'decodeTimezoneDelta',
            'O' => 'decodeTimezoneShortGMT',
            'v' => 'decodeTimezoneNoLocationGeneric',
            'V' => 'decodeTimezoneID',
            'X' => 'decodeTimezoneWithTimeZ',
            'x' => 'decodeTimezoneWithTime',
        );
        $result = '';
        if (!empty($value)) {
            if (!is_a($value, '\DateTime')) {
                throw new \Exception("Invalid value parameter in format");
            }
            $length = is_string($format) ? strlen($format) : 0;
            if ($length === 0) {
                throw new \Exception("Invalid format parameter in format()");
            }
            $cacheKey = empty($locale) ? \Punic\Data::getDefaultLocale() : $locale;
            if (!array_key_exists($cacheKey, $decodeCache)) {
                $decodeCache[$cacheKey] = array();
            }
            if (!array_key_exists($format, $decodeCache[$cacheKey])) {
                $decoder = array();
                $lengthM1 = $length - 1;
                $quoted = false;
                for ($index = 0; $index < $length; $index++) {
                    $char = $format[$index];
                    if ($char === "'") {
                        if ($quoted) {
                            $quoted = false;
                        } elseif (($index < $lengthM1) && ($format[$index + 1] === "'")) {
                            $decoder[] = "'";
                            $index++;
                        } else {
                            $quoted = true;
                        }
                    } elseif ($quoted) {
                        $decoder[] = $char;
                    } else {
                        $count = 1;
                        for ($j = $index + 1; ($j < $length) && ($format[$j] === $char); $j++) {
                            $count++;
                            $index++;
                        }
                        if (array_key_exists($char, $decoderFunctions)) {
                            $decoder[] = array($decoderFunctions[$char], $count);
                        } else {
                            $decoder[] = str_repeat($char, $count);
                        }
                    }
                }
                $decodeCache[$cacheKey][$format] = $decoder;
            } else {
                $decoder = $decodeCache[$cacheKey][$format];
            }
            foreach ($decoder as $chunk) {
                if (is_string($chunk)) {
                    $result .= $chunk;
                } else {
                    $functionName = $chunk[0];
                    $count = $chunk[1];
                    $result .= static::$functionName($value, $count, $locale);
                }
            }
        }

        return $result;
    }
    /**
     * Format a date and/or time (extended version: various date/time representations - see toDateTime())
     * @param \DateTime $value The \DateTime instance for which you want the localized textual representation
     * @param string $format The ISO format that specify how to render the date/time
     * @param string|\DateTimeZone $toTimezone The timezone to set; leave empty to use the default timezone (or the timezone associated to $value if it's already a \DateTime)
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $value is empty, the localized textual representation otherwise
     * @throws \Exception Throws an exception in case of problems
     * @link http://cldr.unicode.org/translation/date-time-patterns
     * @link http://cldr.unicode.org/translation/date-time
     * @link http://www.unicode.org/reports/tr35/tr35-dates.html#Date_Format_Patterns
     */
    public static function formatEx($value, $format, $toTimezone = '', $locale = '')
    {
        return static::format(
            static::toDateTime($value, $toTimezone),
            $format,
            $locale
        );
    }

    protected static function decodeDayOfWeek(\DateTime $value, $count, $locale, $standAlone = false)
    {
        switch ($count) {
            case 1:
            case 2:
            case 3:
                return static::getWeekdayName($value, 'abbreviated', $locale, $standAlone);
            case 4:
                return static::getWeekdayName($value, 'wide', $locale, $standAlone);
            case 5:
                return static::getWeekdayName($value, 'narrow', $locale, $standAlone);
            case 6:
                return static::getWeekdayName($value, 'short', $locale, $standAlone);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeDayOfWeekLocal(\DateTime $value, $count, $locale, $standAlone = false)
    {
        switch ($count) {
            case 1:
            case 2:
                $weekDay = intval($value->format('w'));
                $firstWeekdayForCountry = static::getFirstWeekday($locale);
                $localWeekday = 1 + ((7 + $weekDay - $firstWeekdayForCountry) % 7);

                return str_pad(strval($localWeekday), $count, '0', STR_PAD_LEFT);
            default:
                return static::decodeDayOfWeek($value, $count, $locale, $standAlone);
        }
    }

    protected static function decodeDayOfWeekLocalAlone(\DateTime $value, $count, $locale)
    {
        return static::decodeDayOfWeekLocal($value, $count, $locale, true);
    }

    protected static function decodeDayOfMonth(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return $value->format('j');
            case 2:
                return $value->format('d');
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeMonth(\DateTime $value, $count, $locale, $standAlone = false)
    {
        switch ($count) {
            case 1:
                return $value->format('n');
            case 2:
                return $value->format('m');
            case 3:
                return static::getMonthName($value, 'abbreviated', $locale, $standAlone);
            case 4:
                return static::getMonthName($value, 'wide', $locale, $standAlone);
            case 5:
                return static::getMonthName($value, 'narrow', $locale, $standAlone);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeMonthAlone(\DateTime $value, $count, $locale)
    {
        return static::decodeMonth($value, $count, $locale, true);
    }

    protected static function decodeYear(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return strval(intval($value->format('Y')));
            case 2:
                return $value->format('y');
            default:
                $s = $value->format('Y');
                if ($count > strlen($s)) {
                    $s = str_pad($s, $count, '0', STR_PAD_LEFT);
                }

                return $s;
        }
    }

    protected static function decodeHour12(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return $value->format('g');
            case 2:
                return $value->format('h');
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeDayperiod(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return static::getDayperiodName($value, 'abbreviated', $locale);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeHour24(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return $value->format('G');
            case 2:
                return $value->format('H');
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeHour12From0(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
            case 2:
                return str_pad(strval(intval($value->format('G')) % 12), $count, '0', STR_PAD_LEFT);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeHour24From1(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
            case 2:
                return str_pad(strval(1 + intval($value->format('G'))), $count, '0', STR_PAD_LEFT);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeMinute(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return strval(intval($value->format('i')));
            case 2:
                return $value->format('i');
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeSecond(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return strval(intval($value->format('s')));
            case 2:
                return $value->format('s');
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeTimezoneNoLocationSpecific(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
            case 2:
            case 3:
                $tz = static::getTimezoneNameNoLocationSpecific($value, 'short', '', $locale);
                if (!strlen($tz)) {
                    $tz = static::decodeTimezoneShortGMT($value, 1, $locale);
                }
                break;
            case 4:
                $tz = static::getTimezoneNameNoLocationSpecific($value, 'long', '', $locale);
                if (!strlen($tz)) {
                    $tz = static::decodeTimezoneShortGMT($value, 4, $locale);
                }
                break;
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }

        return $tz;
    }

    protected static function decodeTimezoneShortGMT(\DateTime $value, $count, $locale)
    {
        $offset = $value->getOffset();
        $sign = ($offset < 0) ? '-' : '+';
        $seconds = abs($offset);
        $hours = intval(floor($seconds / 3600));
        $seconds -= $hours * 3600;
        $minutes = intval(floor($seconds / 60));
        $data = \Punic\Data::get('timeZoneNames', $locale);
        $format = array_key_exists('gmtFormat', $data) ? $data['gmtFormat'] : 'GMT%1$s';
        switch ($count) {
            case 1:
                return sprintf($format, $sign . $hours . (($minutes === 0) ? '' :  (':' . substr('0' . $minutes, -2))));
            case 4:
                return sprintf($format, $sign . $hours . ':' . substr('0' . $minutes, -2));
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeEra(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
            case 2:
            case 3:
                return static::getEraName($value, 'abbreviated', $locale);
            case 4:
                return static::getEraName($value, 'wide', $locale);
            case 5:
                return static::getEraName($value, 'narrow', $locale);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeYearWeekOfYear(\DateTime $value, $count, $locale)
    {
        if ($count <= 0) {
            throw new Exception('Invalid count for ' . __METHOD__);
        }
        $y = $value->format('o');
        if ($count === 2) {
            $y = substr('0' . $y, -2);
        } else {
            if ($count > strlen($y)) {
                $y = str_pad($y, $count, '0', STR_PAD_LEFT);
            }
        }

        return $y;
    }

    /**
     * Note: we assume Gregorian calendar here
     */
    protected static function decodeYearExtended(\DateTime $value, $count, $locale)
    {
        return static::decodeYear($value, $count, $locale);
    }

    /**
     * Note: we assume Gregorian calendar here
     */
    protected static function decodeYearRelatedGregorian(\DateTime $value, $count, $locale)
    {
        return static::decodeYearExtended($value, $count, $locale);
    }

    protected static function decodeQuarter(\DateTime $value, $count, $locale, $standAlone = false)
    {
        $quarter = 1 + intval(floor((intval($value->format('n')) - 1) / 3));
        switch ($count) {
            case 1:
                return strval($quarter);
            case 2:
                return '0' . strval($quarter);
            case 3:
                return static::getQuarterName($quarter, 'abbreviated', $locale, $standAlone);
            case 4:
                return static::getQuarterName($quarter, 'wide', $locale, $standAlone);
            case 5:
                return static::getQuarterName($quarter, 'narrow', $locale, $standAlone);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeQuarterAlone(\DateTime $value, $count, $locale)
    {
        return static::decodeQuarter($value, $count, $locale, true);
    }

    protected static function decodeWeekOfYear(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                return strval(intval($value->format('W')));
            case 2:
                return $value->format('W');
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeDayOfYear(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
            case 2:
            case 3:
                return str_pad(strval(1 + $value->format('z')), $count, '0', STR_PAD_LEFT);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeWeekdayInMonth(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
            case 2:
            case 3:
                $dom = intval($value->format('j'));
                $wim = 1 + intval(floor(($dom - 1) / 7));

                return str_pad(strval($wim), $count, '0', STR_PAD_LEFT);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeFranctionsOfSeconds(\DateTime $value, $count, $locale)
    {
        $us = intval($value->format('u'));
        if ($count >= 6) {
            $result = str_pad(strval($us), $count, '0', STR_PAD_LEFT);
        } elseif ($count >= 1) {
            $v = intval(floor($us / pow(10, 6 - $count)));
            $result = str_pad(strval($v), $count, '0', STR_PAD_LEFT);
        } else {
            throw new Exception('Invalid count for ' . __METHOD__);
        }

        return $result;
    }

    protected static function decodeMsecInDay(\DateTime $value, $count, $locale)
    {
        if ($count < 1) {
            throw new Exception('Invalid count for ' . __METHOD__);
        }
        $hours = intval($value->format('G'));
        $minutes = $hours * 60 + intval($value->format('i'));
        $seconds = $minutes * 60 + intval($value->format('s'));
        $milliseconds = $seconds * 60 + intval(floor(intval($value->format('u')) / 1000));

        return str_pad(strval($milliseconds), $count, '0', STR_PAD_LEFT);
    }

    protected static function decodeTimezoneDelta(\DateTime $value, $count, $locale)
    {
        $offset = $value->getOffset();
        $sign = ($offset < 0) ? '-' : '+';
        $seconds = abs($offset);
        $hours = intval(floor($seconds / 3600));
        $seconds -= $hours * 3600;
        $minutes = intval(floor($seconds / 60));
        $seconds -= $minutes * 60;
        $partsWithoutSeconds = array();
        $partsWithoutSeconds[] = $sign . substr('0' . strval($hours), -2);
        $partsWithoutSeconds[] = substr('0' . strval($minutes), -2);
        $partsMaybeWithSeconds = $partsWithoutSeconds;
        if ($seconds > 0) {
            $partsMaybeWithSeconds[] = substr('0' . strval($seconds), -2);
        }
        switch ($count) {
            case 1:
            case 2:
            case 3:
                return implode('', $partsMaybeWithSeconds);
            case 4:
                $data = \Punic\Data::get('timeZoneNames', $locale);
                $format = array_key_exists('gmtFormat', $data) ? $data['gmtFormat'] : 'GMT%1$s';

                return sprintf($format, $sign . implode(':', $partsWithoutSeconds));
            case 5:
                return implode(':', $partsMaybeWithSeconds);
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeTimezoneNoLocationGeneric(\DateTime $value, $count, $locale)
    {
        switch ($count) {
            case 1:
                $tz = static::getTimezoneNameNoLocationSpecific($value, 'short', 'generic', $locale);
                if (!strlen($tz)) {
                    $tz = static::decodeTimezoneID($value, 4, $locale);
                }
                break;
            case 4:
                $tz = static::getTimezoneNameNoLocationSpecific($value, 'long', 'generic', $locale);
                if (!strlen($tz)) {
                    $tz = static::decodeTimezoneID($value, 4, $locale);
                }
                break;
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }

        return $tz;
    }

    protected static function decodeTimezoneID(\DateTime $value, $count, $locale)
    {
        $result = '';
        switch ($count) {
            case 1:
                $result = 'unk';
                break;
            case 2:
                $result = $value->getTimezone()->getName();
                break;
            case 3:
                $result = static::getTimezoneExemplarCity($value, true, $locale);
                break;
            case 4:
                $result = ''; // @todo static::getTimezoneNameLocationSpecific($value, 'short', 'generic', $locale);
                if (!strlen($result)) {
                    $result = static::decodeTimezoneShortGMT($value, 4, $locale);
                }
                break;
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }

        return $result;
    }

    protected static function decodeTimezoneWithTime(\DateTime $value, $count, $locale, $zForZero = false)
    {
        $offset = $value->getOffset();
        $useZ = ($zForZero && ($offset === 0)) ? true : false;
        $sign = ($offset < 0) ? '-' : '+';
        $seconds = abs($offset);
        $hours = intval(floor($seconds / 3600));
        $seconds -= $hours * 3600;
        $minutes = intval(floor($seconds / 60));
        $seconds -= $minutes * 60;
        $hours2 = $sign . substr('0' . strval($hours), -2);
        $minutes2 = substr('0' . strval($minutes), -2);
        $seconds2 = substr('0' . strval($seconds), -2);
        $hmMaybe = array($sign . $hours2);
        if ($minutes > 0) {
            $hmMaybe[] = $minutes2;
        }
        $hmsMaybe = array($sign . $hours2, $minutes2);
        if ($seconds > 0) {
            $hmsMaybe[] = $seconds2;
        }
        switch ($count) {
            case 1:
                $result = $useZ ? 'Z' : implode('', $hmMaybe);
                break;
            case 2:
                $result = $useZ ? 'Z' : "$hours2$minutes2";
                break;
            case 3:
                $result = $useZ ? 'Z' : "$hours2:$minutes2";
                break;
            case 4:
                $result = $useZ ? 'Z' : implode('', $hmsMaybe);
                break;
            case 5:
                $result = $useZ ? 'Z' : implode(':', $hmsMaybe);
                break;
            default:
                throw new Exception('Invalid count for ' . __METHOD__);
        }
    }

    protected static function decodeTimezoneWithTimeZ(\DateTime $value, $count, $locale)
    {
        return static::decodeTimezoneWithTime($value, $count, $locale, true);
    }

    /** @todo */
    protected static function decodeWeekOfMonth(\DateTime $value, $count, $locale) { throw new \Exception('Not implemented'); }
    /** @todo */
    protected static function decodeYearCyclicName() { throw new \Exception('Not implemented'); }
    /** @todo */
    protected static function decodeModifiedGiulianDay() { throw new \Exception('Not implemented'); }
}
