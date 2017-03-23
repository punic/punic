<?php

use \Punic\Calendar;

class ConvertIsoToPhpFormatTest extends PHPUnit_Framework_TestCase
{
    public function providerLiterals()
    {
        return array(
            array(null, null),
            array(false, null),
            array(new stdClass(), null),
            array('', ''),
            array("''", "'"),
            array("'textual'", '\t\e\x\t\u\a\l'),
            array("'a \\ b'", '\a \\\\ \b'),
        );
    }

    /**
     * * @dataProvider providerLiterals
     */
    public function testLiterals($isoFormat, $phpFormat)
    {
        $this->assertSame($phpFormat, Calendar::tryConvertIsoToPhpFormat($isoFormat));
    }

    public function providerLetters()
    {
        $result = array(
            // Era name
            array('G', array(null, null, null, null, null, null)),
            // Calendar year
            array('y', array('Y', 'y', 'Y', 'Y', 'Y', 'Y')),
            // Year in "Week of Year"
            array('Y', array('o', 'o', 'o', 'o', 'o', 'o')),
            // Extended year
            array('u', array('Y', 'Y', 'Y', 'Y', 'Y', 'Y')),
            // Cyclic year name
            array('U', array(null, null, null, null, null, null)),
            // Related Gregorian year
            array('r', array('Y', 'Y', 'Y', 'Y', 'Y', 'Y')),
            // Quarter number/name
            array('Q', array(null, null, null, null, null, null)),
            // Quarter number/name (stand-alone)
            array('q', array(null, null, null, null, null, null)),
            // Month number/name
            array('M', array('n', 'm', 'M', 'F', null, null)),
            // Month number/name (stand-alone)
            array('L', array('n', 'm', 'M', 'F', null, null)),
            // Week of Year
            array('w', array('W', 'W', null, null, null, null)),
            // Week of Month
            array('W', array(null, null, null, null, null, null)),
            // Day of month
            array('d', array('j', 'd', null, null, null, null)),
            // Day of year
            array('D', array('z', 'z', 'z', null, null, null)),
            // Day of Week in Month
            array('F', array(null, null, null, null, null, null)),
            // Modified Julian day
            array('g', array(null, null, null, null, null, null)),
            // Day of week name
            array('E', array('D', 'D', 'D', 'l', null, null)),
            // Local day of week
            array('e', array('N', 'N', 'D', 'l', null, null)),
            // Local day of week (stand-alone)
            array('c', array('N', 'N', 'D', 'l', null, null)),
            // Period AM, PM
            array('a', array('A', 'A', 'A', 'A', null, null)),
            // Period am, pm, noon, midnight
            array('b', array('A', 'A', 'A', 'A', null, null)),
            // Flexible day periods
            array('B', array('A', 'A', 'A', 'A', null, null)),
            // Hour [1-12]
            array('h', array('g', 'h', null, null, null, null)),
            // Hour [0-23]
            array('H', array('G', 'H', null, null, null, null)),
            // Hour [0-11]
            array('K', array(null, null, null, null, null, null)),
            // Hour [1-24]
            array('k', array(null, null, null, null, null, null)),
            // Minute
            array('m', array('i', 'i', null, null, null, null)),
            // Second
            array('s', array('s', 's', null, null, null, null)),
            // Milliseconds in day
            array('A', array(null, null, null, null, null, null)),
            // Zone: short specific non-location format
            array('z', array('T', 'T', 'T', '\G\M\TP', null, null)),
            // Zone: ISO8601 basic format / long localized GMT format
            array('Z', array('O', 'O', 'O', '\G\M\TP', 'P', null)),
            // Zone: localized GMT format
            array('O', array('\G\M\TP', null, null, '\G\M\TP', null, null)),
            // Zone: generic non-location format
            array('v', array(null, null, null, null, null, null)),
            // Zone: time zone ID / exemplar city
            array('V', array(null, 'e', null, null, null, null)),
            // Zone: ISO8601
            array('X', array('O', 'O', 'P', 'O', 'P', null)),
            // Zone: ISO8601
            array('x', array('O', 'O', 'P', 'O', 'P', null)),
            // Punic extension
            array('P', array('N', 'w', 'S', 'z', 't', 'L', 'a', 'B', 'u', 'I', 'Z', 'r', 'U', null, null, null)),
        );
        if (version_compare(PHP_VERSION, '7') >= 0) {
            // Fractional Second
            $result[] = array('S', array(null, null, 'v', null, null, 'u', null));
        } else {
            // Fractional Second
            $result[] = array('S', array(null, null, null, null, null, 'u', null));
        }

        return $result;
    }

    /**
     * * @dataProvider providerLetters
     */
    public function testLetters($letter, $multiplierResults)
    {
        foreach ($multiplierResults as $index => $result) {
            $isoFormat = str_repeat($letter, 1 + $index);
            $this->assertSame($result, Calendar::tryConvertIsoToPhpFormat($isoFormat), "ISO '$isoFormat' should give ".(($result === null) ? 'null' : "'$result'"));
        }
    }

    public function providerLocaleFormats()
    {
        return array(
            array(
                'en_US',
                array('full' => 'l, F j, Y', 'long' => 'F j, Y', 'medium' => 'M j, Y', 'short' => 'n/j/y'),
                array('full' => 'g:i:s A \G\M\TP', 'long' => 'g:i:s A T', 'medium' => 'g:i:s A', 'short' => 'g:i A'),
                array('full' => 'l, F j, Y \a\t g:i:s A \G\M\TP', 'long' => 'F j, Y \a\t g:i:s A T', 'medium' => 'M j, Y, g:i:s A', 'short' => 'n/j/y, g:i A'),
            ),
            array(
                'en_GB',
                array('full' => 'l, j F Y', 'long' => 'j F Y', 'medium' => 'j M Y', 'short' => 'd/m/Y'),
                array('full' => 'H:i:s \G\M\TP', 'long' => 'H:i:s T', 'medium' => 'H:i:s', 'short' => 'H:i'),
                array('full' => 'l, j F Y \a\t H:i:s \G\M\TP', 'long' => 'j F Y \a\t H:i:s T', 'medium' => 'j M Y, H:i:s', 'short' => 'd/m/Y, H:i'),
            ),
            array(
                'it',
                array('full' => 'l j F Y', 'long' => 'j F Y', 'medium' => 'd M Y', 'short' => 'd/m/y'),
                array('full' => 'H:i:s \G\M\TP', 'long' => 'H:i:s T', 'medium' => 'H:i:s', 'short' => 'H:i'),
                array('full' => 'l j F Y H:i:s \G\M\TP', 'long' => 'j F Y H:i:s T', 'medium' => 'd M Y, H:i:s', 'short' => 'd/m/y, H:i'),
            ),
        );
    }

    /**
     * * @dataProvider providerLocaleFormats
     */
    public function testLocaleFormats($localeID, array $phpFormatsDate, array $phpFormatsTime, array $phpFormatsDateTime)
    {
        foreach ($phpFormatsDate as $width => $phpFormat) {
            $isoFormat = Calendar::getDateFormat($width, $localeID);
            $this->assertSame($phpFormat, Calendar::tryConvertIsoToPhpFormat($isoFormat), "$width date format for $localeID: ISO $isoFormat => PHP $phpFormat");
        }
        foreach ($phpFormatsTime as $width => $phpFormat) {
            $isoFormat = Calendar::getTimeFormat($width, $localeID);
            $this->assertSame($phpFormat, Calendar::tryConvertIsoToPhpFormat($isoFormat), "$width time format for $localeID: ISO $isoFormat => PHP $phpFormat");
        }
        foreach ($phpFormatsDateTime as $width => $phpFormat) {
            $isoFormat = Calendar::getDatetimeFormat($width, $localeID);
            $this->assertSame($phpFormat, Calendar::tryConvertIsoToPhpFormat($isoFormat), "$width date/time format for $localeID: ISO $isoFormat => PHP $phpFormat");
        }
    }
}
