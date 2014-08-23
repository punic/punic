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
        $dt = Calendar::toDateTime($time, new \DateTimeZone('Europe/Rome'));
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
            array('', null),
            array("dd MMMM yyyy '' H:mm:ss", "d F Y ' G:i:s"),
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
            Calendar::getEraName(1)
        );
        $this->assertSame(
            'AD',
            Calendar::getEraName(1.0)
        );
        $this->assertSame(
            'AD',
            Calendar::getEraName('1')
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
            'January',
            Calendar::getMonthName(1)
        );
        $this->assertSame(
            'January',
            Calendar::getMonthName(1.0)
        );
        $this->assertSame(
            'January',
            Calendar::getMonthName('1')
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

    public function testExceptionsProvider()
    {
        return array(
            array('getDatetimeFormat', array('invalid-width'), 'Exception'),
            array('getTimeFormat', array('invalid-width'), 'Exception'),
            array('getDateFormat', array('invalid-width'), 'Exception'),
            array('getDatetimeFormat', array('1|2|3|4'), 'Exception'),
            array('format', array(new stdClass(), ''), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 1), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'MMMMMM'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'ddd'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'EEEEEEE'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'hhh'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'aa'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'HHH'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'KKK'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'kkk'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'mmm'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'sss'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'zzzzz'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'OO'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'OOO'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'OOOOO'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'GGGGGG'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'QQQQQQ'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'www'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'DDDD'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'FFFF'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'ZZZZZZ'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'vv'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'vvv'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'vvvvv'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'VVVVV'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'XXXXXX'), 'Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'xxxxxx'), 'Exception'),
            array('getWeekdayName', array(8), 'Exception'),
            array('getWeekdayName', array('test'), 'Exception'),
            array('getWeekdayName', array(1, 'invalid-width'), 'Exception'),
            array('getDayperiodName', array('test'), 'Exception'),
            array('getDayperiodName', array('am', 'invalid-width'), 'Exception'),
            array('toDateTime', array(true), 'Exception'),
            array('getEraName', array('test'), 'Exception'),
            array('getEraName', array(1, 'invalid-width'), 'Exception'),
            array('getMonthName', array('test'), 'Exception'),
            array('getMonthName', array(13), 'Exception'),
            array('getMonthName', array(12, 'invalid-width'), 'Exception'),
            array('getQuarterName', array('test'), 'Exception'),
            array('getQuarterName', array(5), 'Exception'),
            array('getQuarterName', array(1, 'invalid-width'), 'Exception'),
            array('toDateTime', array('2000-01-01', true), 'Exception'),
        );
    }

    /**
     * @dataProvider testExceptionsProvider
     */
    public function testExceptions($method, $parameters, $exception)
    {
        $this->setExpectedException($exception);
        call_user_func_array(array('\Punic\Calendar', $method), $parameters);
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
            'Monday',
            Calendar::getWeekdayName(1)
        );
        $this->assertSame(
            'Monday',
            Calendar::getWeekdayName(1.0)
        );
        $this->assertSame(
            'Monday',
            Calendar::getWeekdayName('1')
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
            Calendar::getQuarterName(1)
        );
        $this->assertSame(
            '1st quarter',
            Calendar::getQuarterName(1.0)
        );
        $this->assertSame(
            '1st quarter',
            Calendar::getQuarterName('1')
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
            Calendar::getDayperiodName(1)
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName(1.0)
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName('1')
        );
        $this->assertSame(
            'AM',
            Calendar::getDayperiodName('am')
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
        $dt = Calendar::toDateTime('1984-01-01 00:00', 'Africa/Casablanca');
        $this->assertSame(
            'Western European Standard Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long')
        );
        $dt = Calendar::toDateTime('1985-01-01 00:00', 'Africa/Casablanca');
        $this->assertSame(
            'Central European Standard Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long')
        );
        $dt = Calendar::toDateTime('1987-01-01 00:00', 'Africa/Casablanca');
        $this->assertSame(
            'Western European Standard Time',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long')
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
        $this->assertSame(
            'Città del Vaticano',
            Calendar::getTimezoneExemplarCity(new \DateTimeZone('Europe/Vatican'), false, 'it')
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

    /**
     * @todo Formats not checked: 'U' (decodeYearCyclicName), 'W' (decodeWeekOfMonth), 'g' (decodeModifiedGiulianDay)
     */
    public function testFormat()
    {
        $dt = Calendar::toDateTime('2010-01-02 23:59:04.123');
        $dt2 = Calendar::toDateTime('2010-01-02 08:01:02');
        $dt3 = Calendar::toDateTime('2010-12-31 08:01:02');
        $this->assertSame(
            '',
            Calendar::format(null, 'G')
        );
        $this->assertSame(
            '',
            Calendar::format(false, 'G')
        );
        $this->assertSame(
            '',
            Calendar::format('', 'G')
        );
        // decodeEra
        $this->assertSame('AD', Calendar::format($dt, 'G'));
        $this->assertSame('AD', Calendar::format($dt, 'GG'));
        $this->assertSame('AD', Calendar::format($dt, 'GGG'));
        $this->assertSame('Anno Domini', Calendar::format($dt, 'GGGG'));
        $this->assertSame('A', Calendar::format($dt, 'GGGGG'));
        $this->assertSame('d.C.', Calendar::format($dt, 'GGGG', 'it'));
        // decodeYear
        $this->assertSame('2010', Calendar::format($dt, 'y'));
        $this->assertSame('10', Calendar::format($dt, 'yy'));
        $this->assertSame('2010', Calendar::format($dt, 'yyy'));
        $this->assertSame('2010', Calendar::format($dt, 'yyyy'));
        $this->assertSame('02010', Calendar::format($dt, 'yyyyy'));
        $this->assertSame('002010', Calendar::format($dt, 'yyyyyy'));
        $this->assertSame('2010', Calendar::format($dt, 'y', 'it'));
        // decodeYearWeekOfYear
        $this->assertSame('2009', Calendar::format($dt, 'Y'));
        $this->assertSame('09', Calendar::format($dt, 'YY'));
        $this->assertSame('2009', Calendar::format($dt, 'YYY'));
        $this->assertSame('2009', Calendar::format($dt, 'YYYY'));
        $this->assertSame('02009', Calendar::format($dt, 'YYYYY'));
        $this->assertSame('002009', Calendar::format($dt, 'YYYYYY'));
        $this->assertSame('2009', Calendar::format($dt, 'Y', 'it'));
        // decodeYearExtended
        $this->assertSame('2010', Calendar::format($dt, 'u'));
        $this->assertSame('10', Calendar::format($dt, 'uu'));
        $this->assertSame('2010', Calendar::format($dt, 'uuu'));
        $this->assertSame('2010', Calendar::format($dt, 'uuuu'));
        $this->assertSame('02010', Calendar::format($dt, 'uuuuu'));
        $this->assertSame('002010', Calendar::format($dt, 'uuuuuu'));
        $this->assertSame('2010', Calendar::format($dt, 'u', 'it'));
        // decodeYearRelatedGregorian
        $this->assertSame('2010', Calendar::format($dt, 'r'));
        $this->assertSame('10', Calendar::format($dt, 'rr'));
        $this->assertSame('2010', Calendar::format($dt, 'rrr'));
        $this->assertSame('2010', Calendar::format($dt, 'rrrr'));
        $this->assertSame('02010', Calendar::format($dt, 'rrrrr'));
        $this->assertSame('002010', Calendar::format($dt, 'rrrrrr'));
        $this->assertSame('2010', Calendar::format($dt, 'r', 'it'));
        // decodeQuarter
        $this->assertSame('1', Calendar::format($dt, 'Q'));
        $this->assertSame('01', Calendar::format($dt, 'QQ'));
        $this->assertSame('Q1', Calendar::format($dt, 'QQQ'));
        $this->assertSame('1st quarter', Calendar::format($dt, 'QQQQ'));
        $this->assertSame('1', Calendar::format($dt, 'QQQQQ'));
        $this->assertSame('I. negyedév', Calendar::format($dt, 'QQQQ', 'hu'));
        // decodeQuarterAlone
        $this->assertSame('1', Calendar::format($dt, 'q'));
        $this->assertSame('01', Calendar::format($dt, 'qq'));
        $this->assertSame('Q1', Calendar::format($dt, 'qqq'));
        $this->assertSame('1st quarter', Calendar::format($dt, 'qqqq'));
        $this->assertSame('1', Calendar::format($dt, 'qqqqq'));
        $this->assertSame('1. negyedév', Calendar::format($dt, 'qqqq', 'hu'));
        // decodeMonth
        $this->assertSame('1', Calendar::format($dt, 'M'));
        $this->assertSame('01', Calendar::format($dt, 'MM'));
        $this->assertSame('Jan', Calendar::format($dt, 'MMM'));
        $this->assertSame('January', Calendar::format($dt, 'MMMM'));
        $this->assertSame('J', Calendar::format($dt, 'MMMMM'));
        $this->assertSame('gennaio', Calendar::format($dt, 'MMMM', 'it'));
        // decodeMonthAlone
        $this->assertSame('1', Calendar::format($dt, 'L'));
        $this->assertSame('01', Calendar::format($dt, 'LL'));
        $this->assertSame('Jan', Calendar::format($dt, 'LLL'));
        $this->assertSame('January', Calendar::format($dt, 'LLLL'));
        $this->assertSame('J', Calendar::format($dt, 'LLLLL'));
        $this->assertSame('Gennaio', Calendar::format($dt, 'LLLL', 'it'));
        // decodeWeekOfYear
        $this->assertSame('53', Calendar::format($dt, 'w'));
        $this->assertSame('53', Calendar::format($dt, 'ww'));
        $this->assertSame('53', Calendar::format($dt, 'w', 'it'));
        // decodeDayOfMonth
        $this->assertSame('2', Calendar::format($dt, 'd'));
        $this->assertSame('02', Calendar::format($dt, 'dd'));
        $this->assertSame('2', Calendar::format($dt, 'd', 'it'));
        // decodeDayOfYear
        $this->assertSame('2', Calendar::format($dt, 'D'));
        $this->assertSame('02', Calendar::format($dt, 'DD'));
        $this->assertSame('002', Calendar::format($dt, 'DDD'));
        $this->assertSame('365', Calendar::format($dt3, 'D'));
        $this->assertSame('365', Calendar::format($dt3, 'D', 'it'));
        // decodeWeekdayInMonth
        $this->assertSame('1', Calendar::format($dt, 'F'));
        $this->assertSame('01', Calendar::format($dt, 'FF'));
        $this->assertSame('001', Calendar::format($dt, 'FFF'));
        $this->assertSame('1', Calendar::format($dt, 'F', 'it'));
        // decodeDayOfWeek
        $this->assertSame('Sat', Calendar::format($dt, 'E'));
        $this->assertSame('Sat', Calendar::format($dt, 'EE'));
        $this->assertSame('Sat', Calendar::format($dt, 'EEE'));
        $this->assertSame('Saturday', Calendar::format($dt, 'EEEE'));
        $this->assertSame('S', Calendar::format($dt, 'EEEEE'));
        $this->assertSame('Sa', Calendar::format($dt, 'EEEEEE'));
        $this->assertSame('sab', Calendar::format($dt, 'E', 'it'));
        // decodeDayOfWeekLocal
        $this->assertSame('7', Calendar::format($dt, 'e'));
        $this->assertSame('07', Calendar::format($dt, 'ee'));
        $this->assertSame('Sat', Calendar::format($dt, 'eee'));
        $this->assertSame('Saturday', Calendar::format($dt, 'eeee'));
        $this->assertSame('S', Calendar::format($dt, 'eeeee'));
        $this->assertSame('Sa', Calendar::format($dt, 'eeeeee'));
        $this->assertSame('6', Calendar::format($dt, 'e', 'it'));
        $this->assertSame('sabato', Calendar::format($dt, 'eeee', 'it'));
        // decodeDayOfWeekLocalAlone
        $this->assertSame('7', Calendar::format($dt, 'c'));
        $this->assertSame('07', Calendar::format($dt, 'cc'));
        $this->assertSame('Sat', Calendar::format($dt, 'ccc'));
        $this->assertSame('Saturday', Calendar::format($dt, 'cccc'));
        $this->assertSame('S', Calendar::format($dt, 'ccccc'));
        $this->assertSame('Sa', Calendar::format($dt, 'cccccc'));
        $this->assertSame('6', Calendar::format($dt, 'c', 'it'));
        $this->assertSame('Sabato', Calendar::format($dt, 'cccc', 'it'));
        // decodeDayperiod
        $this->assertSame('PM', Calendar::format($dt, 'a'));
        $this->assertSame('nachm.', Calendar::format($dt, 'a', 'de'));
        // decodeHour12
        $this->assertSame('11', Calendar::format($dt, 'h'));
        $this->assertSame('11', Calendar::format($dt, 'hh'));
        $this->assertSame('11', Calendar::format($dt, 'h', 'it'));
        // decodeHour24
        $this->assertSame('8', Calendar::format($dt2, 'H'));
        $this->assertSame('08', Calendar::format($dt2, 'HH'));
        $this->assertSame('8', Calendar::format($dt2, 'H', 'it'));
        // decodeHour12From0
        $this->assertSame('8', Calendar::format($dt2, 'K'));
        $this->assertSame('08', Calendar::format($dt2, 'KK'));
        $this->assertSame('8', Calendar::format($dt2, 'K', 'it'));
        // decodeHour24From1
        $this->assertSame('9', Calendar::format($dt2, 'k'));
        $this->assertSame('09', Calendar::format($dt2, 'kk'));
        $this->assertSame('9', Calendar::format($dt2, 'k', 'it'));
        // decodeMinute
        $this->assertSame('1', Calendar::format($dt2, 'm'));
        $this->assertSame('01', Calendar::format($dt2, 'mm'));
        $this->assertSame('1', Calendar::format($dt2, 'm', 'it'));
        // decodeSecond
        $this->assertSame('2', Calendar::format($dt2, 's'));
        $this->assertSame('02', Calendar::format($dt2, 'ss'));
        $this->assertSame('2', Calendar::format($dt2, 's', 'it'));
        // decodeFranctionsOfSeconds
        $this->assertSame('1', Calendar::format($dt, 'S'));
        $this->assertSame('12', Calendar::format($dt, 'SS'));
        $this->assertSame('123', Calendar::format($dt, 'SSS'));
        $this->assertSame('1230', Calendar::format($dt, 'SSSS'));
        $this->assertSame('12300', Calendar::format($dt, 'SSSSS'));
        $this->assertSame('123000', Calendar::format($dt, 'SSSSSS'));
        $this->assertSame('1230000', Calendar::format($dt, 'SSSSSSS'));
        $this->assertSame('1', Calendar::format($dt, 'S', 'it'));
        // decodeMsecInDay
        $this->assertSame('86344123', Calendar::format($dt, 'A'));
        $this->assertSame('86344123', Calendar::format($dt, 'AA'));
        $this->assertSame('86344123', Calendar::format($dt, 'AAA'));
        $this->assertSame('86344123', Calendar::format($dt, 'AAAA'));
        $this->assertSame('86344123', Calendar::format($dt, 'AAAAA'));
        $this->assertSame('0086344123', Calendar::format($dt, 'AAAAAAAAAA'));
        $this->assertSame('86344123', Calendar::format($dt, 'A', 'it'));
        // decodeTimezoneNoLocationSpecific
        $this->assertSame('GMT+13', Calendar::format($dt, 'z'));
        $this->assertSame('GMT+13', Calendar::format($dt, 'zz'));
        $this->assertSame('GMT+13', Calendar::format($dt, 'zzz'));
        $this->assertSame('Fiji Summer Time', Calendar::format($dt, 'zzzz'));
        $this->assertSame('Ora legale delle Fiji', Calendar::format($dt, 'zzzz', 'it'));
        $this->assertSame('GMT-1:02', Calendar::format(Calendar::toDateTime('10/Oct/2000:13:55:36 -0102'), 'zzzz'));
        // decodeTimezoneDelta
        $this->assertSame('+1300', Calendar::format($dt, 'Z'));
        $this->assertSame('+1300', Calendar::format($dt, 'ZZ'));
        $this->assertSame('+1300', Calendar::format($dt, 'ZZZ'));
        $this->assertSame('GMT+13:00', Calendar::format($dt, 'ZZZZ'));
        $this->assertSame('+13:00', Calendar::format($dt, 'ZZZZZ'));
        $this->assertSame('UTC+13:00', Calendar::format($dt, 'ZZZZ', 'fr'));
        // decodeTimezoneShortGMT
        $this->assertSame('GMT+13', Calendar::format($dt, 'O'));
        $this->assertSame('GMT+13:00', Calendar::format($dt, 'OOOO'));
        $this->assertSame('UTC+13', Calendar::format($dt, 'O', 'fr'));
        // decodeTimezoneNoLocationGeneric
        $this->assertSame('GMT+13:00', Calendar::format($dt, 'v'));
        $this->assertSame('Fiji Time', Calendar::format($dt, 'vvvv'));
        $this->assertSame('GMT+14:15', Calendar::format(Calendar::toDateTime('2000-01-01 11:12:13+14:15'), 'vvvv'));
        $this->assertSame('UTC+13:00', Calendar::format($dt, 'v', 'fr'));
        $this->assertSame('heure des îles Fidji', Calendar::format($dt, 'vvvv', 'fr'));
        // decodeTimezoneID
        $this->assertSame('unk', Calendar::format($dt, 'V'));
        $this->assertSame('Pacific/Fiji', Calendar::format($dt, 'VV'));
        $this->assertSame('Fiji', Calendar::format($dt, 'VVV'));
        $this->assertSame('GMT+13:00', Calendar::format($dt, 'VVVV'));
        // decodeTimezoneWithTime
        $this->assertSame('+13', Calendar::format($dt, 'x'));
        $this->assertSame('+1300', Calendar::format($dt, 'xx'));
        $this->assertSame('+13:00', Calendar::format($dt, 'xxx'));
        $this->assertSame('+1300', Calendar::format($dt, 'xxxx'));
        $this->assertSame('+13:00', Calendar::format($dt, 'xxxxx'));
        $this->assertSame('+13:00', Calendar::format($dt, 'xxxxx', 'it'));
        $this->assertSame('-03:30', Calendar::format(Calendar::toDateTime('2000-01-01', 'NST'), 'xxx'));
        // decodeTimezoneWithTimeZ
        $this->assertSame('+13', Calendar::format($dt, 'X'));
        $this->assertSame('+1300', Calendar::format($dt, 'XX'));
        $this->assertSame('+13:00', Calendar::format($dt, 'XXX'));
        $this->assertSame('+1300', Calendar::format($dt, 'XXXX'));
        $this->assertSame('+13:00', Calendar::format($dt, 'XXXXX'));
        // Mixed
        $this->assertSame("2010'01", Calendar::format($dt, "yyyy''MM"));
    }
}
