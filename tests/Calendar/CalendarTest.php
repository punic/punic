<?php
use \Punic\Calendar;

class CalendarTest extends PHPUnit_Framework_TestCase
{
    protected $initialTimezone;
    protected function setUp()
    {
        parent::setUp();
        $this->initialTimezone = date_default_timezone_get();
        if (empty($this->initialTimezone)) {
            $this->initialTimezone = 'UTC';
        }
        if (date_default_timezone_set('Pacific/Fiji') !== true) {
            throw new Exception('Unable to set initial timezone');
        }
        \Punic\Data::setFallbackLocale('en_US');
        \Punic\Data::setDefaultLocale('en_US');
    }
    protected function tearDown()
    {
        if (isset($this->initialTimezone)) {
            @date_default_timezone_set($this->initialTimezone);
        }
    }

    public function testToDateTime()
    {
        /* @var $dt \DateTime */
        /* @var $dt2 \DateTime */
        $dt = Calendar::toDateTime(false);
        $this->assertNull(
            $dt,
            'Check empty value (false)'
        );

        $dt = Calendar::toDateTime('');
        $this->assertNull(
            $dt,
            "Check empty value ('')"
        );

        $dt = Calendar::toDateTime(null);
        $this->assertNull(
            $dt,
            'Check empty value (null)'
        );

        $dt = Calendar::toDateTime(0);
        $this->assertNotNull(
            $dt,
            'Check 0'
        );

        $dt = Calendar::toDateTime('0');
        $this->assertNotNull(
            $dt,
            "Check '0'"
        );

        $dt = Calendar::toDateTime('now');
        $this->assertLessThan(
            3,
            time() - intval($dt->format('U')),
            'Calculating now'
        );

        $time = 1488904200; // 2017-03-07 16:30:00 UTC
        $dt = Calendar::toDateTime($time);
        $this->assertSame(
            '2017-03-08T04:30:00+12:00',
            $dt->format('c'),
            'Calculating from timestamp'
        );

        $time = 1488904200; // 2017-03-07 16:30:00 UTC
        $dt = Calendar::toDateTime($time, 'Europe/Rome');
        $this->assertSame(
            '2017-03-07T17:30:00+01:00',
            $dt->format('c'),
            'Calculating from timestamp to a specific timezone'
        );

        $dt = Calendar::toDateTime('2017-03-01 10:30');
        $this->assertSame(
            '2017-03-01T10:30:00+12:00',
            $dt->format('c'),
            'Calculating from string'
        );

        $dt = Calendar::toDateTime('2017-03-01 10:30', 'Europe/Rome');
        $this->assertSame(
            '2017-02-28T23:30:00+01:00',
            $dt->format('c'),
            'Calculating from string to a specific timezone'
        );

        $dt = new DateTime('2017-12-01 18:30');
        $dt2 = Calendar::toDateTime($dt);
        $this->assertSame(
            '2017-12-01T18:30:00+13:00',
            $dt2->format('c'),
            'Calculating from DateTime'
        );

        $dt = new DateTime('2017-12-01 00:15');
        $dt2 = Calendar::toDateTime($dt, 'Asia/Tokyo');
        $this->assertSame(
            '2017-11-30T20:15:00+09:00',
            $dt2->format('c'),
            'Calculating from DateTime to a specific timezone'
        );
    }

    public function providerConvertPhpToIso()
    {
        return array(
            array('dd', 'd'),
            array('EE', 'D'),
            array('d', 'j'),
            array('EEEE', 'l'),
            array('eee', 'N'),
            array('SS', 'S'),
            array('e', 'w'),
            array('D', 'z'),
            array('ww', 'W'),
            array('MMMM', 'F'),
            array('MM', 'm'),
            array('MMM', 'M'),
            array('M', 'n'),
            array('ddd', 't'),
            array('l', 'L'),
            array('YYYY', 'o'),
            array('yyyy', 'Y'),
            array('yy', 'y'),
            array('a', 'a'),
            array('a', 'A'),
            array('B', 'B'),
            array('h', 'g'),
            array('H', 'G'),
            array('hh', 'h'),
            array('HH', 'H'),
            array('mm', 'i'),
            array('ss', 's'),
            array('zzzz', 'e'),
            array('I', 'I'),
            array('Z', 'O'),
            array('ZZZZ', 'P'),
            array('z', 'T'),
            array('X', 'Z'),
            array('yyyy-MM-ddTHH:mm:ssZZZZ', 'c'),
            array('r', 'r'),
            array('U', 'U'),
            array('HHmmss', 'His'),
            array("dd MMMM yyyy 'alle' H:mm:ss", 'd F Y \a\l\l\e G:i:s'),
        );
    }

    /**
     * test convertPhpToIso
     * expected boolean
     * @dataProvider providerConvertPhpToIso
     */
    public function testConvertPhpToIso($a, $b)
    {
        $this->assertSame($a, Calendar::convertPhpToIsoFormat($b));
    }

    public function testGetEraName()
    {
        $this->assertSame(
            '',
            Calendar::getEraName(null)
        );
        $this->assertSame(
            '',
            Calendar::getEraName('')
        );
        $this->assertSame(
            '',
            Calendar::getEraName(false)
        );
        $this->assertSame(
            'AD',
            Calendar::getEraName(2000)
        );
        $this->assertSame(
            'Anno Domini',
            Calendar::getEraName(2000, 'wide')
        );
        $this->assertSame(
            'AD',
            Calendar::getEraName(2000, 'abbreviated')
        );
        $this->assertSame(
            'A',
            Calendar::getEraName(2000, 'narrow')
        );
        $this->assertSame(
            'dC',
            Calendar::getEraName(2000, 'narrow', 'it')
        );
    }

    public function testGetMonthName()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07');
        $this->assertSame(
            '',
            Calendar::getMonthName(null)
        );
        $this->assertSame(
            '',
            Calendar::getMonthName('')
        );
        $this->assertSame(
            '',
            Calendar::getMonthName(false)
        );
        $this->assertSame(
            'March',
            Calendar::getMonthName($dt)
        );
        $this->assertSame(
            'March',
            Calendar::getMonthName($dt, 'wide')
        );
        $this->assertSame(
            'Mar',
            Calendar::getMonthName($dt, 'abbreviated')
        );
        $this->assertSame(
            'M',
            Calendar::getMonthName($dt, 'narrow')
        );
        $this->assertSame(
            'marzo',
            Calendar::getMonthName($dt, 'wide', 'it')
        );
        $this->assertSame(
            'marzo',
            Calendar::getMonthName($dt, 'wide', 'it', false)
        );
        $this->assertSame(
            'Marzo',
            Calendar::getMonthName($dt, 'wide', 'it', true)
        );
    }

    public function testGetWeekdayName()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07');
        $this->assertSame(
            '',
            Calendar::getWeekdayName(null)
        );
        $this->assertSame(
            '',
            Calendar::getWeekdayName('')
        );
        $this->assertSame(
            '',
            Calendar::getWeekdayName(false)
        );
        $this->assertSame(
            'Sunday',
            Calendar::getWeekdayName($dt)
        );
        $this->assertSame(
            'Sunday',
            Calendar::getWeekdayName($dt, 'wide')
        );
        $this->assertSame(
            'Sun',
            Calendar::getWeekdayName($dt, 'abbreviated')
        );
        $this->assertSame(
            'Su',
            Calendar::getWeekdayName($dt, 'short')
        );
        $this->assertSame(
            'S',
            Calendar::getWeekdayName($dt, 'narrow')
        );
        $this->assertSame(
            'domenica',
            Calendar::getWeekdayName($dt, 'wide', 'it')
        );
        $this->assertSame(
            'domenica',
            Calendar::getWeekdayName($dt, 'wide', 'it', false)
        );
        $this->assertSame(
            'Domenica',
            Calendar::getWeekdayName($dt, 'wide', 'it', true)
        );
    }

    public function testGetQuarterName()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07');
        $this->assertSame(
            '',
            Calendar::getQuarterName(null)
        );
        $this->assertSame(
            '',
            Calendar::getQuarterName('')
        );
        $this->assertSame(
            '',
            Calendar::getQuarterName(false)
        );
        $this->assertSame(
            '1st quarter',
            Calendar::getQuarterName($dt)
        );
        $this->assertSame(
            '1st quarter',
            Calendar::getQuarterName($dt, 'wide')
        );
        $this->assertSame(
            'Q1',
            Calendar::getQuarterName($dt, 'abbreviated')
        );
        $this->assertSame(
            '1',
            Calendar::getQuarterName($dt, 'narrow')
        );
        $this->assertSame(
            'I. negyedév',
            Calendar::getQuarterName($dt, 'wide', 'hu')
        );
        $this->assertSame(
            'I. negyedév',
            Calendar::getQuarterName($dt, 'wide', 'hu', false)
        );
        $this->assertSame(
            '1. negyedév',
            Calendar::getQuarterName($dt, 'wide', 'hu', true)
        );
    }

    public function testGetDayperiodName()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07');
        $this->assertSame(
            '',
            Calendar::getDayperiodName(null)
        );
        $this->assertSame(
            '',
            Calendar::getDayperiodName('')
        );
        $this->assertSame(
            '',
            Calendar::getDayperiodName(false)
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName($dt)
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName($dt, 'wide')
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName($dt, 'abbreviated')
        );
        $this->assertSame(
            'a',
            Calendar::getDayperiodName($dt, 'narrow')
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName($dt, 'wide', 'it')
        );
        $this->assertSame(
            'm.',
            Calendar::getDayperiodName($dt, 'narrow', 'it')
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName($dt, 'wide', 'it', false)
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName($dt, 'wide', 'it', true)
        );
    }

    public function testGetTimezoneNameNoLocationSpecific()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07');
        $this->assertSame(
            '',
            Calendar::getTimezoneNameNoLocationSpecific(null)
        );
        $this->assertSame(
            '',
            Calendar::getTimezoneNameNoLocationSpecific('')
        );
        $this->assertSame(
            '',
            Calendar::getTimezoneNameNoLocationSpecific(false)
        );
        $this->assertSame(
            'Fiji Summer Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt)
        );
        $this->assertSame(
            'Fiji Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long', 'generic')
        );
        $this->assertSame(
            'Fiji Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt->getTimezone())
        );
        $this->assertSame(
            'Fiji Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt->getTimezone()->getName())
        );
        $this->assertSame(
            'Greenwich Mean Time',
            Calendar::getTimezoneNameNoLocationSpecific('GMT', 'long')
        );
        $this->assertSame(
            'GMT',
            Calendar::getTimezoneNameNoLocationSpecific('GMT', 'short')
        );
        $dt = Calendar::toDateTime('2010-03-07', 'Europe/Rome');
        $this->assertSame(
            'Central European Standard Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long')
        );
        $dt = Calendar::toDateTime('2010-08-07', 'Europe/Rome');
        $this->assertSame(
            'Central European Summer Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long')
        );
        $this->assertSame(
            'Central European Time',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/Rome', 'long', 'generic')
        );
        $this->assertSame(
            'Central European Standard Time',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/Rome', 'long', 'standard')
        );
        $this->assertSame(
            'Central European Summer Time',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/Rome', 'long', 'daylight')
        );
        $this->assertSame(
            "Ora legale dell'Europa centrale",
            Calendar::getTimezoneNameNoLocationSpecific('Europe/Rome', 'long', 'daylight', 'it')
        );
        $this->assertSame(
            'CET',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/Rome', 'short', 'generic', 'it')
        );
        $this->assertSame(
            'CET',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/Rome', 'short', 'standard', 'it')
        );
        $this->assertSame(
            'CEST',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/Rome', 'short', 'daylight', 'it')
        );
    }

    public function testGetTimezoneExemplarCity()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07');
        $this->assertSame(
            'Fiji',
            Calendar::getTimezoneExemplarCity($dt)
        );
        $this->assertSame(
            'Unknown City',
            Calendar::getTimezoneExemplarCity('This is a bad timezone name')
        );
        $this->assertSame(
            'Unknown City',
            Calendar::getTimezoneExemplarCity('This is a bad timezone name', true)
        );
        $this->assertSame(
            '',
            Calendar::getTimezoneExemplarCity('This is a bad timezone name', false)
        );
        $this->assertSame(
            'Vatican',
            Calendar::getTimezoneExemplarCity('Europe/Vatican')
        );
        $this->assertSame(
            'Città del Vaticano',
            Calendar::getTimezoneExemplarCity('Europe/Vatican', false, 'it')
        );
    }

    public function testHas12HoursClock()
    {
        $this->assertSame(
            true,
            Calendar::has12HoursClock('en')
        );
        $this->assertSame(
            false,
            Calendar::has12HoursClock('it')
        );
    }

    public function testGetFirstWeekday()
    {
        $this->assertSame(
            0, // Sunday
            Calendar::getFirstWeekday('en')
        );
        $this->assertSame(
            0, // Sunday
            Calendar::getFirstWeekday('en_US')
        );
        $this->assertSame(
            1, // Monday
            Calendar::getFirstWeekday('en_IT')
        );
        $this->assertSame(
            1, // Monday
            Calendar::getFirstWeekday('it')
        );
        $this->assertSame(
            1, // Monday
            Calendar::getFirstWeekday('it_IT')
        );
    }

    public function testGetDateFormat()
    {
        $this->assertSame(
            'EEEE, MMMM d, y',
            Calendar::getDateFormat('full')
        );
        $this->assertSame(
            'EEEE, MMMM d, y',
            Calendar::getDateFormat('full', 'en_US')
        );
        $this->assertSame(
            'MMMM d, y',
            Calendar::getDateFormat('long', 'en_US')
        );
        $this->assertSame(
            'MMM d, y',
            Calendar::getDateFormat('medium', 'en_US')
        );
        $this->assertSame(
            'M/d/yy',
            Calendar::getDateFormat('short', 'en_US')
        );
        $this->assertSame(
            'EEEE d MMMM y',
            Calendar::getDateFormat('full', 'it')
        );
        $this->assertSame(
            'dd MMMM y',
            Calendar::getDateFormat('long', 'it')
        );
        $this->assertSame(
            'dd/MMM/y',
            Calendar::getDateFormat('medium', 'it_IT')
        );
        $this->assertSame(
            'dd/MM/yy',
            Calendar::getDateFormat('short', 'it_IT')
        );
    }

    public function testGetTimeFormat()
    {
        $this->assertSame(
            'h:mm:ss a zzzz',
            Calendar::getTimeFormat('full')
        );
        $this->assertSame(
            'h:mm:ss a zzzz',
            Calendar::getTimeFormat('full', 'en_US')
        );
        $this->assertSame(
            'h:mm:ss a z',
            Calendar::getTimeFormat('long', 'en_US')
        );
        $this->assertSame(
            'h:mm:ss a',
            Calendar::getTimeFormat('medium', 'en_US')
        );
        $this->assertSame(
            'h:mm a',
            Calendar::getTimeFormat('short', 'en_US')
        );
        $this->assertSame(
            'HH:mm:ss zzzz',
            Calendar::getTimeFormat('full', 'it')
        );
        $this->assertSame(
            'HH:mm:ss z',
            Calendar::getTimeFormat('long', 'it')
        );
        $this->assertSame(
            'HH:mm:ss',
            Calendar::getTimeFormat('medium', 'it_IT')
        );
        $this->assertSame(
            'HH:mm',
            Calendar::getTimeFormat('short', 'it_IT')
        );
    }

    public function testGetDatetimeFormat()
    {
        $this->assertSame(
            "EEEE, MMMM d, y 'at' h:mm:ss a zzzz",
            Calendar::getDatetimeFormat('full')
        );
        $this->assertSame(
            "MMMM d, y 'at' h:mm:ss a z",
            Calendar::getDatetimeFormat('long')
        );
        $this->assertSame(
            "MMM d, y, h:mm:ss a",
            Calendar::getDatetimeFormat('medium')
        );
        $this->assertSame(
            "M/d/yy, h:mm a",
            Calendar::getDatetimeFormat('short')
        );
        $this->assertSame(
            "EEEE, MMMM d, y 'at' h:mm a",
            Calendar::getDatetimeFormat('full|short')
        );
        $this->assertSame(
            "M/d/yy 'at' h:mm:ss a zzzz",
            Calendar::getDatetimeFormat('short|full')
        );
        $this->assertSame(
            "M/d/yy 'at' h:mm a",
            Calendar::getDatetimeFormat('full|short|short')
        );
        $this->assertSame(
            "EEEE, MMMM d, y, h:mm:ss a zzzz",
            Calendar::getDatetimeFormat('short|full|full')
        );
    }

    public function testFormatDate()
    {
        $dt = Calendar::toDateTime('2010-10-12 23:59');
        $this->assertSame(
            'Tuesday, October 12, 2010',
            Calendar::formatDate($dt, 'full')
        );
        $this->assertSame(
            'October 12, 2010',
            Calendar::formatDate($dt, 'long')
        );
        $this->assertSame(
            'Oct 12, 2010',
            Calendar::formatDate($dt, 'medium')
        );
        $this->assertSame(
            '10/12/10',
            Calendar::formatDate($dt, 'short')
        );
        $this->assertSame(
            'martedì 12 ottobre 2010',
            Calendar::formatDate($dt, 'full', 'it')
        );
        $this->assertSame(
            '12 ottobre 2010',
            Calendar::formatDate($dt, 'long', 'it')
        );
        $this->assertSame(
            '12/ott/2010',
            Calendar::formatDate($dt, 'medium', 'it')
        );
        $this->assertSame(
            '12/10/10',
            Calendar::formatDate($dt, 'short', 'it')
        );
    }

    public function testFormatDateEx()
    {
        $this->assertSame(
            'Tuesday, October 12, 2010',
            Calendar::formatDateEx('2010-10-12 23:59', 'full', 'Europe/Rome')
        );
        $this->assertSame(
            'Monday, October 11, 2010',
            Calendar::formatDateEx('2010-10-12 00:00', 'full', 'Europe/Rome')
        );
    }

    public function testFormatTime()
    {
        $dt = Calendar::toDateTime('2010-10-12 23:59');
        $this->assertSame(
            '11:59:00 PM Fiji Standard Time',
            Calendar::formatTime($dt, 'full')
        );
        $this->assertSame(
            '11:59:00 PM GMT+12',
            Calendar::formatTime($dt, 'long')
        );
        $this->assertSame(
            '11:59:00 PM',
            Calendar::formatTime($dt, 'medium')
        );
        $this->assertSame(
            '11:59 PM',
            Calendar::formatTime($dt, 'short')
        );
        $this->assertSame(
            '23:59:00 Ora standard delle Fiji',
            Calendar::formatTime($dt, 'full', 'it')
        );
        $this->assertSame(
            '23:59:00 GMT+12',
            Calendar::formatTime($dt, 'long', 'it')
        );
        $this->assertSame(
            '23:59:00',
            Calendar::formatTime($dt, 'medium', 'it')
        );
        $this->assertSame(
            '23:59',
            Calendar::formatTime($dt, 'short', 'it')
        );
    }

    public function testFormatTimeEx()
    {
        $this->assertSame(
            '1:59:00 PM Central European Summer Time',
            Calendar::formatTimeEx('2010-10-12 23:59', 'full', 'Europe/Rome')
        );
        $this->assertSame(
            '2:00:00 PM Central European Summer Time',
            Calendar::formatTimeEx('2010-10-12 00:00', 'full', 'Europe/Rome')
        );
    }

    public function testFormatDateTime()
    {
        $dt = Calendar::toDateTime('2010-10-12 23:59');
        $this->assertSame(
            'Tuesday, October 12, 2010 at 11:59:00 PM Fiji Standard Time',
            Calendar::formatDateTime($dt, 'full')
        );
        $this->assertSame(
            'October 12, 2010 at 11:59:00 PM GMT+12',
            Calendar::formatDateTime($dt, 'long')
        );
        $this->assertSame(
            'Oct 12, 2010, 11:59:00 PM',
            Calendar::formatDateTime($dt, 'medium')
        );
        $this->assertSame(
            '10/12/10, 11:59 PM',
            Calendar::formatDateTime($dt, 'short')
        );
        $this->assertSame(
            'Tuesday, October 12, 2010 at 11:59 PM',
            Calendar::formatDateTime($dt, 'full|short')
        );
        $this->assertSame(
            'Tuesday, October 12, 2010, 11:59 PM',
            Calendar::formatDateTime($dt, 'short|full|short')
        );
        $this->assertSame(
            'martedì 12 ottobre 2010 23:59:00 Ora standard delle Fiji',
            Calendar::formatDateTime($dt, 'full', 'it')
        );
        $this->assertSame(
            '12 ottobre 2010 23:59:00 GMT+12',
            Calendar::formatDateTime($dt, 'long', 'it')
        );
        $this->assertSame(
            '12/ott/2010 23:59:00',
            Calendar::formatDateTime($dt, 'medium', 'it')
        );
        $this->assertSame(
            '12/10/10 23:59',
            Calendar::formatDateTime($dt, 'short', 'it')
        );
    }

    public function testFormatDateTimeEx()
    {
        $this->assertSame(
            'Tuesday, October 12, 2010 at 1:59:00 PM Central European Summer Time',
            Calendar::formatDateTimeEx('2010-10-12 23:59', 'full', 'Europe/Rome')
        );
        $this->assertSame(
            'Monday, October 11, 2010 at 2:00:00 PM Central European Summer Time',
            Calendar::formatDateTimeEx('2010-10-12 00:00', 'full', 'Europe/Rome')
        );
    }

}
