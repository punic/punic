<?php

use \Punic\Calendar;

class ConvertPhpToIsoFormatTest extends PHPUnit_Framework_TestCase
{
    private static function timestampToGMDateTime($timestamp)
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone('UTC'));
        $dateTime->setTimestamp($timestamp);

        return $dateTime;
    }
    public function providerConvertPhpToIsoFormat()
    {
        $chunks = array(
            // Day
            'd', 'D', 'j', 'l', 'N', 'S', 'w', 'z',
            // Week
            'W',
            // Month
            'F', 'm', 'M', 'n', 't',
            // Year
            'L', 'o', 'Y', 'y',
            // Time
            'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u',
            // Timezone
            'e', 'I', 'O', 'P', 'T', 'Z',
            // Full Date/Time
            'c', 'r', 'U',
        );
        $dateTimeNow = self::timestampToGMDateTime($timestampNow = time());
        $dateTimeFirstDay = self::timestampToGMDateTime($timestampFirstDay = strtotime('2000-01-01T00:00:00+00:00'));
        $dateTimeLastDay = self::timestampToGMDateTime($timestampLastDay = strtotime('2000-12-31T23:59:59+00:00'));

        $result = array();
        foreach ($chunks as $chunk) {
            $result[] = array($chunk, $timestampNow, $dateTimeNow);
            $result[] = array($chunk, $timestampFirstDay, $dateTimeFirstDay);
            $result[] = array($chunk, $timestampLastDay, $dateTimeLastDay);
        }

        return $result;
    }
    /**
     * @dataProvider providerConvertPhpToIsoFormat
     */
    public function testConvertPhpToIsoFormat($phpFormat, $timestamp, DateTime $dateTime)
    {
        $punicFormat = Calendar::convertPhpToIsoFormat($phpFormat);
        $this->assertSame(
            gmdate($phpFormat, $timestamp),
            Calendar::format($dateTime, $punicFormat),
            "PHP date/time format chunk '$phpFormat' converted as '$punicFormat' and rendered for ".$dateTime->format('c')
        );
    }
}
