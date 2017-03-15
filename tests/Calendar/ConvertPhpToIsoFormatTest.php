<?php

use \Punic\Calendar;

class ConvertPhpToIsoFormatTest extends PHPUnit_Framework_TestCase
{
    const TEST_TIMEZONE = 'America/Los_Angeles';
    private $previousTimezone;

    protected function setUp()
    {
        $this->previousTimezone = @date_default_timezone_get();
        if (empty($this->previousTimezone)) {
            $this->previousTimezone = 'UTC';
        }
        date_default_timezone_set(self::TEST_TIMEZONE);
    }

    protected function tearDown()
    {
        date_default_timezone_set($this->previousTimezone);
    }

    private static function timestampToDateTime($timestamp)
    {
        $dateTime = new DateTime();
        $dateTime->setTimezone(new DateTimeZone(self::TEST_TIMEZONE));
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
        $dateTimeNow = self::timestampToDateTime($timestampNow = time());
        $dateTimeFirstDay = self::timestampToDateTime($timestampFirstDay = strtotime('2000-01-01T00:00:00'));
        $dateTimeLastDay = self::timestampToDateTime($timestampLastDay = strtotime('2000-12-31T23:59:59'));

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
            date($phpFormat, $timestamp),
            Calendar::format($dateTime, $punicFormat, 'en'),
            "PHP date/time format chunk '$phpFormat' converted as '$punicFormat' and rendered for ".$dateTime->format('c')
        );
    }
}
