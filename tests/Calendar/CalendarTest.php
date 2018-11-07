<?php

use Punic\Calendar;

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
            throw new \Exception('Unable to set initial timezone');
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
            time() - (int) $dt->format('U'),
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
        $this->assertSame(
            '2000-01-01T00:00:00+01:00',
            Calendar::toDateTime('2000-01-01 00:00', 'Europe/Rome', 'Europe/Rome')->format('c')
        );
        $this->assertSame(
            '2000-01-01T09:30:00+10:30',
            Calendar::toDateTime('2000-01-01 00:00', 'Australia/Adelaide', 'Europe/Rome')->format('c')
        );
        $this->assertSame(
            '1999-12-31T14:30:00+01:00',
            Calendar::toDateTime('2000-01-01 00:00', 'Europe/Rome', 'Australia/Adelaide')->format('c')
        );
        $this->assertSame(
            '1999-12-31T14:30:00+01:00',
            Calendar::toDateTime('2000-01-01 00:00', 'Europe/Rome', new \DateTimeZone('Australia/Adelaide'))->format('c')
        );
        $this->assertSame(
            '1999-12-31T14:30:00+01:00',
            Calendar::toDateTime('2000-01-01 00:00', new \DateTimeZone('Europe/Rome'), 'Australia/Adelaide')->format('c')
        );
        $this->assertSame(
            '1999-12-31T14:30:00+01:00',
            Calendar::toDateTime('2000-01-01 00:00', new \DateTimeZone('Europe/Rome'), new \DateTimeZone('Australia/Adelaide'))->format('c')
        );
        $this->assertSame(
            '2000-01-01T01:00:00+01:00',
            Calendar::toDateTime('2000-01-01T00:00:00+00:00', 'Europe/Rome', 'Australia/Adelaide')->format('c')
        );
        $time = 1488904200; // 2017-03-07 16:30:00 UTC
        $this->assertSame(
            '2017-03-07T17:30:00+01:00',
            Calendar::toDateTime($time, null, 'Europe/Rome')->format('c'),
            'Calculating from timestamp'
        );
        $this->assertSame(
            '2017-03-08T03:00:00+10:30',
            Calendar::toDateTime((string) $time, null, 'Australia/Adelaide')->format('c'),
            'Calculating from timestamp'
        );
        $this->assertSame(
            '2017-03-08T03:00:00+10:30',
            Calendar::toDateTime(new \DateTime('2017-03-07T16:30:00+00:00'), null, 'Australia/Adelaide')->format('c'),
            'Calculating from timestamp'
        );

        if (version_compare(PHP_VERSION, '5.5') >= 0) {
            $this->assertSame(
                '2017-03-08T03:00:00+10:30',
                Calendar::toDateTime(new \DateTimeImmutable('2017-03-07T16:30:00+00:00'), null, 'Australia/Adelaide')->format('c'),
                'Calculating from timestamp'
            );
        }
    }

    /**
     * @return array
     */
    public function providerConvertPhpToIso()
    {
        return array(
            array("dd MMMM yyyy 'alle' H:mm:ss", 'd F Y \a\l\l\e G:i:s'),
            array('', null),
            array("dd MMMM yyyy '' H:mm:ss", "d F Y ' G:i:s"),
        );
    }

    /**
     * test convertPhpToIso
     * expected boolean.
     *
     * @dataProvider providerConvertPhpToIso
     *
     * @param string $a
     * @param string $b
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
            'marzo',
            Calendar::getMonthName($dt, 'wide', 'it', true)
        );
    }

    public function testExceptionsProvider()
    {
        return array(
            array('getDatetimeFormat', array('invalid-width'), '\\Punic\\Exception'),
            array('getTimeFormat', array('invalid-width'), '\\Punic\\Exception'),
            array('getDateFormat', array('invalid-width'), '\\Punic\\Exception'),
            array('getDatetimeFormat', array('1|2|3|4'), '\\Punic\\Exception'),
            array('getSkeletonFormat', array('invalid-skeleton'), '\\Punic\\Exception'),
            array('getSkeletonFormat', array('yE'), '\\Punic\\Exception'),
            array('getIntervalFormat', array('E', 'y'), '\\Punic\\Exception'),
            array('getIntervalFormat', array('_', 'y'), '\\Punic\\Exception'),
            array('format', array(new stdClass(), ''), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 1), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'MMMMMM'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'ddd'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'EEEEEEE'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'hhh'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'aaaaaa'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'bbbbbb'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'BBBBBB'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'HHH'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'KKK'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'kkk'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'mmm'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'sss'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'zzzzz'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'OO'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'OOO'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'OOOOO'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'GGGGGG'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'QQQQQQ'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'www'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'DDDD'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'FFFF'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'ZZZZZZ'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'vv'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'vvv'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'vvvvv'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'VVVVV'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'XXXXXX'), '\\Punic\\Exception'),
            array('format', array(Calendar::toDateTime('2010-01-02 08:01:02'), 'xxxxxx'), '\\Punic\\Exception'),
            array('getTimezoneNameLocationSpecific', array(1), '\\Punic\\Exception'),
            array('getWeekdayName', array(8), '\\Punic\\Exception'),
            array('getWeekdayName', array('test'), '\\Punic\\Exception'),
            array('getWeekdayName', array(1, 'invalid-width'), '\\Punic\\Exception'),
            array('getDayperiodName', array(25), '\\Punic\\Exception'),
            array('getDayperiodName', array('test'), '\\Punic\\Exception'),
            array('getDayperiodName', array('am', 'invalid-width'), '\\Punic\\Exception'),
            array('getVariableDayperiodName', array(25), '\\Punic\\Exception'),
            array('getVariableDayperiodName', array('test'), '\\Punic\\Exception'),
            array('getVariableDayperiodName', array('morning1', 'invalid-width'), '\\Punic\\Exception'),
            array('toDateTime', array(true), '\\Punic\\Exception'),
            array('toDateTime', array('this is an invalid date representation'), '\\Punic\\Exception'),
            array('toDateTime', array('now', 'this is an invalid timezone representation'), '\\Punic\\Exception'),
            array('getEraName', array('test'), '\\Punic\\Exception'),
            array('getEraName', array(1, 'invalid-width'), '\\Punic\\Exception'),
            array('getMonthName', array('test'), '\\Punic\\Exception'),
            array('getMonthName', array(13), '\\Punic\\Exception'),
            array('getMonthName', array(12, 'invalid-width'), '\\Punic\\Exception'),
            array('getQuarterName', array('test'), '\\Punic\\Exception'),
            array('getQuarterName', array(5), '\\Punic\\Exception'),
            array('getQuarterName', array(1, 'invalid-width'), '\\Punic\\Exception'),
            array('toDateTime', array('2000-01-01', true), '\\Punic\\Exception'),
            array('toDateTime', array('2000-01-01', 'This is an invalid *to* timezone'), '\\Punic\\Exception'),
            array('toDateTime', array('2000-01-01', 'Europe/Rome', 'This is an invalid *from* timezone'), '\\Punic\\Exception'),
            array('toDateTime', array('2000-01-01', 'Europe/Rome', true), '\\Punic\\Exception'),
            array('getDeltaDays', array('string'), '\\Punic\\Exception'),
            array('getDeltaDays', array(new \DateTime(), 'string'), '\\Punic\\Exception'),
            array('describeInterval', array('not-a-datetime'), '\\Punic\\Exception'),
            array('describeInterval', array(new \DateTime(), 'not-a-datetime'), '\\Punic\\Exception'),
        );
    }

    /**
     * @dataProvider testExceptionsProvider
     *
     * @param string $method
     * @param array $parameters
     * @param string $exception
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
            'domenica',
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

    public function testGetVariableDayperiodName()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07 13:00');
        $this->assertSame(
            '',
            Calendar::getVariableDayperiodName(null)
        );
        $this->assertSame(
            '',
            Calendar::getVariableDayperiodName('')
        );
        $this->assertSame(
            '',
            Calendar::getVariableDayperiodName(false)
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName(13)
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName(13.0)
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName('13')
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName($dt)
        );
        $this->assertSame(
            'at night',
            Calendar::getVariableDayperiodName(0)
        );
        $this->assertSame(
            'in the morning',
            Calendar::getVariableDayperiodName(6)
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName(12)
        );
        $this->assertSame(
            'in the evening',
            Calendar::getVariableDayperiodName(18)
        );
        $this->assertSame(
            'in the evening',
            Calendar::getVariableDayperiodName(23)
        );
        $this->assertSame(
            'de la madrugada',
            Calendar::getVariableDayperiodName(0, 'wide', 'es')
        );
        $this->assertSame(
            'de la mañana',
            Calendar::getVariableDayperiodName(6, 'wide', 'es')
        );
        $this->assertSame(
            'de la tarde',
            Calendar::getVariableDayperiodName(12, 'wide', 'es')
        );
        $this->assertSame(
            'de la noche',
            Calendar::getVariableDayperiodName(20, 'wide', 'es')
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName($dt, 'wide')
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName($dt, 'abbreviated')
        );
        $this->assertSame(
            'in the afternoon',
            Calendar::getVariableDayperiodName($dt, 'narrow')
        );
        $this->assertSame(
            'på eftermiddagen',
            Calendar::getVariableDayperiodName($dt, 'wide', 'sv')
        );
        $this->assertSame(
            'på efterm.',
            Calendar::getVariableDayperiodName($dt, 'narrow', 'sv')
        );
        $this->assertSame(
            'på eftermiddagen',
            Calendar::getVariableDayperiodName($dt, 'wide', 'sv', false)
        );
        $this->assertSame(
            'efterm.',
            Calendar::getVariableDayperiodName($dt, 'narrow', 'sv', true)
        );
        $this->assertSame(
            'eftermiddag',
            Calendar::getVariableDayperiodName($dt, 'wide', 'sv', true)
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
            Calendar::getTimezoneNameNoLocationSpecific('invalid timezone')
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

        $dt = Calendar::toDateTime('2000-01-01 11:12:13', 'Etc/GMT+2');
        $this->assertSame(
            '',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long')
        );
        $dt = Calendar::toDateTime('2000-01-01 11:12:13+14:15');
        $this->assertSame(
            '',
            Calendar::getTimezoneNameNoLocationSpecific($dt, 'long')
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
            'Greenwich Mean Time',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/London', 'long', 'generic')
        );
        $this->assertSame(
            'Greenwich Mean Time',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/London', 'long', 'standard')
        );
        $this->assertSame(
            'British Summer Time',
            Calendar::getTimezoneNameNoLocationSpecific('Europe/London', 'long', 'daylight')
        );
        $this->assertSame(
            'British Summer Time',
            Calendar::getTimezoneNameNoLocationSpecific(new DateTimeZone('Europe/London'), 'long', 'daylight')
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
            'Ora legale dell’Europa centrale',
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

    public function testGetTimezoneNameLocationSpecific()
    {
        /* @var $dt \DateTime */
        $dt = Calendar::toDateTime('2010-03-07');
        $this->assertSame(
            '',
            Calendar::getTimezoneNameLocationSpecific(null)
        );
        $this->assertSame(
            '',
            Calendar::getTimezoneNameLocationSpecific('')
        );
        $this->assertSame(
            '',
            Calendar::getTimezoneNameLocationSpecific('invalid timezone')
        );
        $this->assertSame(
            '',
            Calendar::getTimezoneNameLocationSpecific(false)
        );
        $this->assertSame(
            'Fiji Time',
            Calendar::getTimezoneNameLocationSpecific($dt)
        );
        $this->assertSame(
            'Fiji Time',
            Calendar::getTimezoneNameLocationSpecific($dt->getTimezone())
        );
        $this->assertSame(
            'Fiji Time',
            Calendar::getTimezoneNameLocationSpecific($dt->getTimezone()->getName())
        );
        $this->assertSame(
            '',
            Calendar::getTimezoneNameLocationSpecific('GMT')
        );

        $dt = Calendar::toDateTime('2000-01-01 11:12:13', 'Etc/GMT+2');
        $this->assertSame(
            '',
            Calendar::getTimezoneNameLocationSpecific($dt)
        );
        $dt = Calendar::toDateTime('2000-01-01 11:12:13+14:15');
        $this->assertSame(
            '',
            Calendar::getTimezoneNameLocationSpecific($dt)
        );

        // Timezone in primaryZones.json.
        $dt = Calendar::toDateTime('2010-03-07', 'Europe/Berlin');
        $this->assertSame(
            'Germany Time',
            Calendar::getTimezoneNameLocationSpecific($dt)
        );
        $this->assertSame(
            'Germany Time',
            Calendar::getTimezoneNameLocationSpecific($dt->getTimezone())
        );
        $this->assertSame(
            'Germany Time',
            Calendar::getTimezoneNameLocationSpecific($dt->getTimezone()->getName())
        );

        // Country with multiple timezones.
        $dt = Calendar::toDateTime('2010-03-07', 'America/New_York');
        $this->assertSame(
            'New York Time',
            Calendar::getTimezoneNameLocationSpecific($dt)
        );
        $this->assertSame(
            'New York Time',
            Calendar::getTimezoneNameLocationSpecific($dt->getTimezone())
        );
        $this->assertSame(
            'New York Time',
            Calendar::getTimezoneNameLocationSpecific($dt->getTimezone()->getName())
        );

        // Timezone with alias.
        $this->assertSame(
            'Atikokan Time',
            Calendar::getTimezoneNameLocationSpecific('America/Atikokan')
        );
        $this->assertSame(
            'Atikokan Time',
            Calendar::getTimezoneNameLocationSpecific('America/Coral_Harbour')
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
            'MMM y G',
            Calendar::getTimeFormat('~GyMMM')
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
            'd MMMM y',
            Calendar::getDateFormat('long', 'it')
        );
        $this->assertSame(
            'd MMM y',
            Calendar::getDateFormat('medium', 'it_IT')
        );
        $this->assertSame(
            'dd/MM/yy',
            Calendar::getDateFormat('short', 'it_IT')
        );
        $this->assertSame(
            'G y. MMM',
            Calendar::getTimeFormat('~GyMMM', 'hu')
        );
    }

    public function testGetTimeFormat()
    {
        $this->assertSame(
            'h:mm:ss a zzzz',
            Calendar::getTimeFormat('full')
        );
        $this->assertSame(
            'h:mm a',
            Calendar::getTimeFormat('~hm')
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
        $this->assertSame(
            'a h:mm',
            Calendar::getTimeFormat('~hm', 'hu')
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
            'MMM d, y, h:mm:ss a',
            Calendar::getDatetimeFormat('medium')
        );
        $this->assertSame(
            'M/d/yy, h:mm a',
            Calendar::getDatetimeFormat('short')
        );
        $this->assertSame(
            'MMM y G',
            Calendar::getDatetimeFormat('~GyMMM')
        );
        $this->assertSame(
            "EEEE, MMMM d, y 'at' h:mm a",
            Calendar::getDatetimeFormat('full|short')
        );
        $this->assertSame(
            'M/d/yy, h:mm:ss a zzzz',
            Calendar::getDatetimeFormat('short|full')
        );
        $this->assertSame(
            "MMMM d 'at' h:mm a",
            Calendar::getDatetimeFormat('~MMMMd|~hm')
        );
        $this->assertSame(
            'E, MMM d, y, h:mm:ss a zzzz',
            Calendar::getDatetimeFormat('~yMMMEd|full')
        );
        $this->assertSame(
            "EEE, d 'de' MMMM 'de' y, h:mm a",
            Calendar::getDatetimeFormat('~yMMMMEd|~hm', 'es')
        );
        $this->assertSame(
            "M/d/yy 'at' h:mm a",
            Calendar::getDatetimeFormat('full|short|short')
        );
        $this->assertSame(
            'EEEE, MMMM d, y, h:mm:ss a zzzz',
            Calendar::getDatetimeFormat('short|full|full')
        );
        $this->assertSame(
            "MMM y G 'at' h:mm a",
            Calendar::getDatetimeFormat('full|~GyMMM|~hm')
        );
        $this->assertSame(
            'MMM y G, h:mm a',
            Calendar::getDatetimeFormat('short|~GyMMM|~hm')
        );
    }

    public function testGetSkeletonFormat()
    {
        $this->assertSame(
            'MMM y G',
            Calendar::getSkeletonFormat('GyMMM')
        );
        $this->assertSame(
            'MMM y G',
            Calendar::getSkeletonFormat('GyMMM', 'en_US')
        );
        $this->assertSame(
            'G y. MMM',
            Calendar::getSkeletonFormat('GyMMM', 'hu')
        );

        // Non-perfect matches.
        $this->assertSame(
            'MMMM d, y',
            Calendar::getSkeletonFormat('yMMMMd')
        );
        $this->assertSame(
            'Q y',
            Calendar::getSkeletonFormat('yQ')
        );
        $this->assertSame(
            'LLLL y G',
            Calendar::getSkeletonFormat('GyMMMM', 'fi')
        );
        $this->assertSame(
            'cc d. MMM',
            Calendar::getSkeletonFormat('MMMEEd', 'fi')
        );

        // Fractional second.
        $this->assertSame(
            'h:mm:ss.SSS a v',
            Calendar::getSkeletonFormat('hmsSSSv')
        );
        $this->assertSame(
            'h.mm.ss,SSS a v',
            Calendar::getSkeletonFormat('hmsSSSv', 'da')
        );

        // Special input skeleton fields.
        $this->assertSame(
            'h:mm a',
            Calendar::getSkeletonFormat('jm')
        );
        $this->assertSame(
            'hh:mm aaaaa',
            Calendar::getSkeletonFormat('jjjjjjm')
        );
        $this->assertSame(
            "HH 'Uhr'",
            Calendar::getSkeletonFormat('j', 'de')
        );
        $this->assertSame(
            'h:mm:ss a',
            Calendar::getSkeletonFormat('jms', 'en_CN')
        );
        $this->assertSame(
            'hh:mm:ss a',
            Calendar::getSkeletonFormat('jjms', 'en_CN')
        );
        $this->assertSame(
            'h:mm:ss aaaa',
            Calendar::getSkeletonFormat('jjjms', 'en_CN')
        );
        $this->assertSame(
            'hh:mm:ss aaaaa',
            Calendar::getSkeletonFormat('jjjjjjms', 'en_CN')
        );
        $this->assertSame(
            'hh:mm',
            Calendar::getSkeletonFormat('Jm')
        );
        $this->assertSame(
            "HH 'Uhr'",
            Calendar::getSkeletonFormat('J', 'de')
        );
        $this->assertSame(
            'hh',
            Calendar::getSkeletonFormat('J', 'en_CN')
        );
        $this->assertSame(
            'h a',
            Calendar::getSkeletonFormat('C')
        );
        $this->assertSame(
            'hh a',
            Calendar::getSkeletonFormat('CC')
        );
        $this->assertSame(
            'h aaaa',
            Calendar::getSkeletonFormat('CCC')
        );
        $this->assertSame(
            "HH 'Uhr'",
            Calendar::getSkeletonFormat('C', 'de')
        );
        $this->assertSame(
            'h B',
            Calendar::getSkeletonFormat('C', 'en_CN')
        );
        $this->assertSame(
            'hh BBBBB',
            Calendar::getSkeletonFormat('CCCCCC', 'en_CN')
        );

        // Date and time in same skeleton.
        $this->assertSame(
            'M/d/y, h:mm a',
            Calendar::getSkeletonFormat('yMdhm')
        );
        $this->assertSame(
            'd.M.y, h:mm a',
            Calendar::getSkeletonFormat('yMdhm', 'de')
        );
        $this->assertSame(
            'M/d/y, h:mm a',
            Calendar::getSkeletonFormat('yMdjm')
        );
        $this->assertSame(
            'd.M.y, HH:mm',
            Calendar::getSkeletonFormat('yMdjm', 'de')
        );
        $this->assertSame(
            'M/d/y, HH:mm',
            Calendar::getSkeletonFormat('yMdJm')
        );
        $this->assertSame(
            'd.M.y, HH:mm',
            Calendar::getSkeletonFormat('yMdJm', 'de')
        );
        $this->assertSame(
            'M/d/y, h:mm a',
            Calendar::getSkeletonFormat('yMdCm')
        );
        $this->assertSame(
            'd.M.y, HH:mm',
            Calendar::getSkeletonFormat('yMdCm', 'de')
        );
        $this->assertSame(
            "MMMM d, y 'at' h:mm a",
            Calendar::getSkeletonFormat('yMMMMdhm')
        );
    }

    public function testGetIntervalFormat()
    {
        $this->assertSame(
            array('MMM d', null),
            Calendar::getIntervalFormat('MMMd', 'H')
        );
        $this->assertSame(
            array('MMM d – d', true),
            Calendar::getIntervalFormat('MMMd', 'd')
        );
        $this->assertSame(
            array('MMM d – MMM d', true),
            Calendar::getIntervalFormat('MMMd', 'M')
        );
        $this->assertSame(
            array('MMM d – MMM d', true),
            Calendar::getIntervalFormat('MMMd', 'y')
        );

        $this->assertSame(
            array('h:mm a v – h:mm a v', true),
            Calendar::getIntervalFormat('hmv', 'd')
        );
        $this->assertSame(
            array('h:mm a – h:mm a v', true),
            Calendar::getIntervalFormat('hmv', 'a')
        );
        $this->assertSame(
            array('h:mm – h:mm a v', true),
            Calendar::getIntervalFormat('hmv', 'H')
        );
        $this->assertSame(
            array('h:mm – h:mm a v', true),
            Calendar::getIntervalFormat('hmv', 'm')
        );
        $this->assertSame(
            array('h:mm a v', null),
            Calendar::getIntervalFormat('hmv', 's')
        );

        $this->assertSame(
            array('HH:mm v – HH:mm v', true),
            Calendar::getIntervalFormat('Hmv', 'd')
        );
        $this->assertSame(
            array('HH:mm – HH:mm v', true),
            Calendar::getIntervalFormat('Hmv', 'a')
        );
        $this->assertSame(
            array('HH:mm – HH:mm v', true),
            Calendar::getIntervalFormat('Hmv', 'H')
        );
        $this->assertSame(
            array('HH:mm – HH:mm v', true),
            Calendar::getIntervalFormat('Hmv', 'm')
        );
        $this->assertSame(
            array('HH:mm v', null),
            Calendar::getIntervalFormat('Hmv', 's')
        );

        // Non-perfect matches.
        $this->assertSame(
            array('MMMM d, y – MMMM d, y', true),
            Calendar::getIntervalFormat('yMMMMd', 'G')
        );
        $this->assertSame(
            array('MMMM d, y – MMMM d, y', true),
            Calendar::getIntervalFormat('yMMMMd', 'y')
        );
        $this->assertSame(
            array('MMMM d – MMMM d, y', true),
            Calendar::getIntervalFormat('yMMMMd', 'Q')
        );
        $this->assertSame(
            array('MMMM d – MMMM d, y', true),
            Calendar::getIntervalFormat('yMMMMd', 'M')
        );
        $this->assertSame(
            array('MMMM d – d, y', true),
            Calendar::getIntervalFormat('yMMMMd', 'd')
        );
        $this->assertSame(
            array('MMMM d, y', null),
            Calendar::getIntervalFormat('yMMMMd', 'H')
        );
        $this->assertSame(
            array('Q y – Q y', true),
            Calendar::getIntervalFormat('yQ', 'G')
        );
        $this->assertSame(
            array('Q y – Q y', true),
            Calendar::getIntervalFormat('yQ', 'y')
        );
        $this->assertSame(
            array('Q y – Q y', true),
            Calendar::getIntervalFormat('yQ', 'Q')
        );
        $this->assertSame(
            array('Q y', null),
            Calendar::getIntervalFormat('yQ', 'M')
        );

        // Fractional second.
        $this->assertSame(
            array('h:mm:ss.SSS a – h:mm:ss.SSS a', true),
            Calendar::getIntervalFormat('hmsSSS', 's')
        );
        $this->assertSame(
            array('h:mm:ss.SSS a – h:mm:ss.SSS a', true),
            Calendar::getIntervalFormat('hmsSSS', 'S')
        );

        // Special input skeleton fields.
        $this->assertSame(
            array('h:mm a v – h:mm a v', true),
            Calendar::getIntervalFormat('jmv', 'd')
        );
        $this->assertSame(
            array('h:mm a – h:mm a v', true),
            Calendar::getIntervalFormat('jmv', 'a')
        );
        $this->assertSame(
            array('h:mm – h:mm a v', true),
            Calendar::getIntervalFormat('jmv', 'H')
        );
        $this->assertSame(
            array('h:mm – h:mm a v', true),
            Calendar::getIntervalFormat('jmv', 'm')
        );
        $this->assertSame(
           array('h:mm a v', null),
            Calendar::getIntervalFormat('jmv', 's')
        );
        $this->assertSame(
            array('HH:mm v – HH:mm v', true),
            Calendar::getIntervalFormat('jmv', 'd', 'de')
        );
        $this->assertSame(
           array("HH:mm–HH:mm 'Uhr' v", true),
            Calendar::getIntervalFormat('jmv', 'a', 'de')
        );
        $this->assertSame(
           array("HH:mm–HH:mm 'Uhr' v", true),
            Calendar::getIntervalFormat('jmv', 'H', 'de')
        );
        $this->assertSame(
           array("HH:mm–HH:mm 'Uhr' v", true),
            Calendar::getIntervalFormat('jmv', 'm', 'de')
        );
        $this->assertSame(
            array('HH:mm v', null),
            Calendar::getIntervalFormat('jmv', 's', 'de')
        );
        $this->assertSame(
            array('h a', null),
            Calendar::getIntervalFormat('C', 's')
        );
        $this->assertSame(
            array("HH 'Uhr'", null),
            Calendar::getIntervalFormat('C', 's', 'de')
        );
        $this->assertSame(
            array('y G – y G', true),
            Calendar::getIntervalFormat('Gy', 'G')
        );
        $this->assertSame(
            array('y G – y G', true),
            Calendar::getIntervalFormat('Gy', 'y')
        );
        $this->assertSame(
            array('y G', null),
            Calendar::getIntervalFormat('Gy', 'M')
        );

        // Combined date and time.
        $this->assertSame(
            array('M/d/y, HH:mm v – M/d/y, HH:mm v', true),
            Calendar::getIntervalFormat('yMdHmv', 'y')
        );
        $this->assertSame(
            array('M/d/y, HH:mm v – M/d/y, HH:mm v', true),
            Calendar::getIntervalFormat('yMdHmv', 'd')
        );
        $this->assertSame(
            array('M/d/y, HH:mm v – HH:mm v', true),
            Calendar::getIntervalFormat('yMdHmv', 'H')
        );
        $this->assertSame(
            array('M/d/y, HH:mm v – HH:mm v', true),
            Calendar::getIntervalFormat('yMdHmv', 'm')
        );
        $this->assertSame(
            array('M/d/y, HH:mm v', null),
            Calendar::getIntervalFormat('yMdHmv', 's')
        );
        $this->assertSame(
            array("MMMM d, y 'at' HH:mm v – MMMM d, y 'at' HH:mm v", true),
            Calendar::getIntervalFormat('yMMMMdHmv', 'y')
        );
        $this->assertSame(
            array("MMMM d, y 'at' HH:mm v – MMMM d, y 'at' HH:mm v", true),
            Calendar::getIntervalFormat('yMMMMdHmv', 'd')
        );
        $this->assertSame(
            array("MMMM d, y 'at' HH:mm v – HH:mm v", true),
            Calendar::getIntervalFormat('yMMMMdHmv', 'H')
        );
        $this->assertSame(
            array("MMMM d, y 'at' HH:mm v – HH:mm v", true),
            Calendar::getIntervalFormat('yMMMMdHmv', 'm')
        );
        $this->assertSame(
            array("MMMM d, y 'at' HH:mm v", null),
            Calendar::getIntervalFormat('yMMMMdHmv', 's')
        );
    }

    public function testFormatDate()
    {
        $today = Calendar::toDateTime('now');
        $tomorrow = Calendar::toDateTime('+1 days');
        $yesterday = Calendar::toDateTime('-1 days');
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
            '10/12/2010',
            Calendar::formatDate($dt, '~yMd')
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
            '12 ott 2010',
            Calendar::formatDate($dt, 'medium', 'it')
        );
        $this->assertSame(
            '12/10/10',
            Calendar::formatDate($dt, 'short', 'it')
        );
        $this->assertSame(
            '12/10/2010',
            Calendar::formatDate($dt, '~yMd', 'it')
        );
        $this->assertSame(
            'today',
            Calendar::formatDate($today, 'short*', 'en')
        );
        $this->assertSame(
            'Today',
            Calendar::formatDate($today, 'short^', 'en')
        );
        $this->assertSame(
            'yesterday',
            Calendar::formatDate($yesterday, 'long*', 'en')
        );
        $this->assertSame(
            'Tomorrow',
            Calendar::formatDate($tomorrow, 'narrow^', 'en')
        );
        $this->assertSame(
            'Tomorrow',
            Calendar::formatDate($tomorrow, '~yMd^', 'en')
        );
        $this->assertSame(
            'domani',
            Calendar::formatDate($tomorrow, 'short*', 'it')
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
            '11:59 PM',
            Calendar::formatTime($dt, '~hm')
        );
        $this->assertSame(
            '23:59',
            Calendar::formatTime($dt, '~Hm')
        );
        $this->assertSame(
            '23:59:00 Ora standard delle Figi',
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
        $this->assertSame(
            '23:59',
            Calendar::formatTime($dt, '~Hm', 'it')
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
        $yesterday = Calendar::toDateTime('-1 days');
        $yesterday->setTime(14, 15, 16);
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
            '10/12/2010',
            Calendar::formatDateTime($dt, '~yMd')
        );
        $this->assertSame(
            '11:59 PM',
            Calendar::formatDateTime($dt, '~hm')
        );
        $this->assertSame(
            'Tuesday, October 12, 2010 at 11:59 PM',
            Calendar::formatDateTime($dt, 'full|short')
        );
        $this->assertSame(
            'Tuesday, October 12, 2010 at 23:59',
            Calendar::formatDateTime($dt, 'full|~Hm')
        );
        $this->assertSame(
            '10/12/2010, 11:59 PM',
            Calendar::formatDateTime($dt, '~yMd|short')
        );
        $this->assertSame(
            'Tuesday, October 12, 2010, 11:59 PM',
            Calendar::formatDateTime($dt, 'short|full|short')
        );
        $this->assertSame(
            'martedì 12 ottobre 2010 23:59:00 Ora standard delle Figi',
            Calendar::formatDateTime($dt, 'full', 'it')
        );
        $this->assertSame(
            '12 ottobre 2010 23:59:00 GMT+12',
            Calendar::formatDateTime($dt, 'long', 'it')
        );
        $this->assertSame(
            '12 ott 2010, 23:59:00',
            Calendar::formatDateTime($dt, 'medium', 'it')
        );
        $this->assertSame(
            '12/10/10, 23:59',
            Calendar::formatDateTime($dt, 'short', 'it')
        );
        $this->assertSame(
            'mar 12 ott',
            Calendar::formatDateTime($dt, '~MMMEd', 'it')
        );
        $this->assertSame(
            'Yesterday at 2:15 PM',
            Calendar::formatDateTime($yesterday, 'full|short^|short', 'en')
        );
        $this->assertSame(
            'Ieri 14:15',
            Calendar::formatDateTime($yesterday, 'full|short^|short', 'it')
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

    public function testFormatInterval()
    {
        $dt = Calendar::toDateTime('2010-10-12 22:58:00');
        $dtMilliSecond = Calendar::toDateTime('2010-10-12 22:58:00.001');
        $dtSecond = Calendar::toDateTime('2010-10-12 22:58:01');
        $dtMinute = Calendar::toDateTime('2010-10-12 22:59:00');
        $dtHour = Calendar::toDateTime('2010-10-12 23:57:00');
        $dtDay = Calendar::toDateTime('2010-10-13 21:30:00');
        $dtMonth = Calendar::toDateTime('2010-11-14 20:30:00');
        $dtYear = Calendar::toDateTime('2011-09-15 19:30:00');
        $dtEra = Calendar::toDateTime('-1000-01-01 15:30:00');

        $this->assertSame(
            'January 1, -1000 – October 12, 2010',
            Calendar::formatInterval($dtEra, $dt, 'yMMMMd')
        );
        $this->assertSame(
            'October 12, 2010 – September 15, 2011',
            Calendar::formatInterval($dt, $dtYear, 'yMMMMd')
        );
        $this->assertSame(
            'October 12 – 13, 2010',
            Calendar::formatInterval($dt, $dtDay, 'yMMMMd')
        );
        $this->assertSame(
            'October 12, 2010',
            Calendar::formatInterval($dt, $dtHour, 'yMMMMd')
        );

        $this->assertSame(
            '10/12/2010 – 9/15/2011',
            Calendar::formatInterval($dt, $dtYear, 'yMd')
        );
        $this->assertSame(
            '10/12/2010 – 10/13/2010',
            Calendar::formatInterval($dt, $dtDay, 'yMd')
        );
        $this->assertSame(
            '10/12/2010',
            Calendar::formatInterval($dt, $dtHour, 'yMd')
        );

        $this->assertSame(
            '22:58:00 – 23:57:00',
            Calendar::formatInterval($dt, $dtHour, 'Hms')
        );
        $this->assertSame(
            '22:58:00 – 22:59:00',
            Calendar::formatInterval($dt, $dtMinute, 'Hms')
        );
        $this->assertSame(
            '22:58:00 – 22:58:01',
            Calendar::formatInterval($dt, $dtSecond, 'Hms')
        );
        $this->assertSame(
            '22:58:00',
            Calendar::formatInterval($dt, $dtMilliSecond, 'Hms')
        );

        $this->assertSame(
            '22:58:00.00',
            Calendar::formatInterval($dt, $dtMilliSecond, 'HmsSS')
        );
        $this->assertSame(
            '22:58:00.000 – 22:58:00.001',
            Calendar::formatInterval($dt, $dtMilliSecond, 'HmsSSS')
        );
        $this->assertSame(
            '22:58:00.000000 – 22:58:00.001000',
            Calendar::formatInterval($dt, $dtMilliSecond, 'HmsSSSSSS')
        );
        $this->assertSame(
            '22:58:00.000',
            Calendar::formatInterval($dt, $dt, 'HmsSSS')
        );

        $this->assertSame(
            '12.–13. Oktober 2010',
            Calendar::formatInterval($dt, $dtDay, 'yMMMMd', 'de')
        );
        $this->assertSame(
            '12.10.2010',
            Calendar::formatInterval($dt, $dtHour, 'yMd', 'de')
        );

        // Combined date and time.
        $this->assertSame(
            '10/12/2010, 22:58:00 – 23:57:00',
            Calendar::formatInterval($dt, $dtHour, 'yMdHms')
        );
        $this->assertSame(
            'October 12, 2010 at 22:58:00 – 23:57:00',
            Calendar::formatInterval($dt, $dtHour, 'yMMMMdHms')
        );
        $this->assertSame(
            '10/12/2010, 22:58:00 – 10/13/2010, 21:30:00',
            Calendar::formatInterval($dt, $dtDay, 'yMdHms')
        );
        $this->assertSame(
            'October 12, 2010 at 22:58:00 – October 13, 2010 at 21:30:00',
            Calendar::formatInterval($dt, $dtDay, 'yMMMMdHms')
        );
        $this->assertSame(
            '12 ottobre 2010 22:58:00 - 13 ottobre 2010 21:30:00',
            Calendar::formatInterval($dt, $dtDay, 'yMMMMdHms', 'it')
        );
        $this->assertSame(
            '10/12/2010, 22:58:00',
            Calendar::formatInterval($dt, $dt, 'yMdHms')
        );

        // Special input skeleton fields.
        $this->assertSame(
            '10:58 – 11:57 PM Fiji Time',
            Calendar::formatInterval($dt, $dtHour, 'jmv')
        );
        $this->assertSame(
            '22:58–23:57 Uhr Fidschi Zeit',
            Calendar::formatInterval($dt, $dtHour, 'jmv', 'de')
        );
        $this->assertSame(
            '10:58 – 11:57 Fiji Time',
            Calendar::formatInterval($dt, $dtHour, 'Jmv')
        );
        $this->assertSame(
            '22:58–23:57 Uhr Fidschi Zeit',
            Calendar::formatInterval($dt, $dtHour, 'Jmv', 'de')
        );
        $this->assertSame(
            '10:58 – 11:57 Fiji Time',
            Calendar::formatInterval($dt, $dtHour, 'Jmv', 'en_CN')
        );
        $this->assertSame(
            '10:58 – 11:57 PM Fiji Time',
            Calendar::formatInterval($dt, $dtHour, 'Cmv')
        );
        $this->assertSame(
            '22:58–23:57 Uhr Fidschi Zeit',
            Calendar::formatInterval($dt, $dtHour, 'Cmv', 'de')
        );
    }

    public function testFormatIntervalEx()
    {
        $dt = '2010-10-12 22:58:00 Europe/Berlin';
        $dtMonth = '2010-11-14 20:30:00 Europe/Berlin';
        $dtMilliSecond = '2010-10-12 22:58:00.001 Europe/Berlin';

        $this->assertSame(
            'October 12 – November 14, 2010',
            Calendar::formatIntervalEx($dt, $dtMonth, 'yMMMMd')
        );
        $this->assertSame(
            '12. Oktober – 14. November 2010',
            Calendar::formatIntervalEx($dt, $dtMonth, 'yMMMMd', null, 'de')
        );
        $this->assertSame(
            '22:58:00.00 Germany Time',
            Calendar::formatIntervalEx($dt, $dtMilliSecond, 'HmsSSv', 'Europe/Berlin')
        );
        $this->assertSame(
            '22:58:00,00 heure : Allemagne',
            Calendar::formatIntervalEx($dt, $dtMilliSecond, 'HmsSSv', 'Europe/Berlin', 'fr_FR')
        );
    }

    /**
     * @todo Formats not checked: 'U' (decodeYearCyclicName), 'W' (decodeWeekOfMonth), 'g' (decodeModifiedGiulianDay)
     */
    public function testFormat()
    {
        $dt = Calendar::toDateTime('2010-01-02 23:59:04.0123');
        $dt2 = Calendar::toDateTime('2010-01-02 08:01:02');
        $dt3 = Calendar::toDateTime('2010-12-31 08:01:02');
        $dt4 = Calendar::toDateTime('2010-12-31 08:01:02', 'Etc/GMT-2');
        $dt5 = Calendar::toDateTime('2010-12-31 08:01:02+02:00');
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
        $this->assertSame('d.C.', Calendar::format($dt, 'G', 'it'));
        $this->assertSame('d.C.', Calendar::format($dt, 'GG', 'it'));
        $this->assertSame('d.C.', Calendar::format($dt, 'GGG', 'it'));
        $this->assertSame('dopo Cristo', Calendar::format($dt, 'GGGG', 'it'));
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
        $this->assertSame('gennaio', Calendar::format($dt, 'LLLL', 'it'));
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
        $this->assertSame('sabato', Calendar::format($dt, 'cccc', 'it'));
        // decodeDayperiod
        $this->assertSame('PM', Calendar::format($dt, 'a'));
        $this->assertContains(Calendar::format($dt, 'a', 'de'), array('nachm.', 'PM')); // 'nachm.' for CLDR < 33, 'PM' for CLDR 33.1
        $this->assertSame('PM', Calendar::format($dt, 'aa'));
        $this->assertSame('PM', Calendar::format($dt, 'aaa'));
        $this->assertSame('AM', Calendar::format($dt2, 'aaaa'));
        $this->assertSame('PM', Calendar::format($dt, 'aaaa'));
        $this->assertSame('a', Calendar::format($dt2, 'aaaaa'));
        $this->assertSame('p', Calendar::format($dt, 'aaaaa'));
        $this->assertSame('PM', Calendar::format($dt, 'b'));
        $this->assertSame('PM', Calendar::format($dt, 'bb'));
        $this->assertSame('PM', Calendar::format($dt, 'bbb'));
        $this->assertSame('PM', Calendar::format($dt, 'bbbb'));
        $this->assertSame('p', Calendar::format($dt, 'bbbbb'));
        $this->assertSame('AM', Calendar::format($dt2, 'bbbb'));
        $this->assertSame('a', Calendar::format($dt2, 'bbbbb'));
        $this->assertContains(Calendar::format($dt, 'b', 'de'), array('nachm.', 'PM')); // 'nachm.' for CLDR < 33, 'PM' for CLDR 33.1
        // decodeVariableDayperiod
        $this->assertSame('in the evening', Calendar::format($dt, 'B'));
        $this->assertSame('in the evening', Calendar::format($dt, 'BB'));
        $this->assertSame('in the evening', Calendar::format($dt, 'BBB'));
        $this->assertSame('in the evening', Calendar::format($dt, 'BBBB'));
        $this->assertSame('in the evening', Calendar::format($dt, 'BBBBB'));
        $this->assertSame('in the morning', Calendar::format($dt2, 'BBBB'));
        $this->assertSame('in the morning', Calendar::format($dt2, 'BBBBB'));
        $this->assertSame('abends', Calendar::format($dt, 'B', 'de'));
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
        // decodeFractionsOfSeconds
        $this->assertSame('0', Calendar::format($dt, 'S'));
        $this->assertSame('01', Calendar::format($dt, 'SS'));
        $this->assertSame('012', Calendar::format($dt, 'SSS'));
        $this->assertSame('0123', Calendar::format($dt, 'SSSS'));
        $this->assertSame('01230', Calendar::format($dt, 'SSSSS'));
        $this->assertSame('012300', Calendar::format($dt, 'SSSSSS'));
        $this->assertSame('0123000', Calendar::format($dt, 'SSSSSSS'));
        $this->assertSame('0', Calendar::format($dt, 'S', 'it'));
        // decodeMsecInDay
        $this->assertSame('86344012', Calendar::format($dt, 'A'));
        $this->assertSame('86344012', Calendar::format($dt, 'AA'));
        $this->assertSame('86344012', Calendar::format($dt, 'AAA'));
        $this->assertSame('86344012', Calendar::format($dt, 'AAAA'));
        $this->assertSame('86344012', Calendar::format($dt, 'AAAAA'));
        $this->assertSame('0086344012', Calendar::format($dt, 'AAAAAAAAAA'));
        $this->assertSame('86344012', Calendar::format($dt, 'A', 'it'));
        // decodeTimezoneNoLocationSpecific
        $this->assertSame('GMT+13', Calendar::format($dt, 'z'));
        $this->assertSame('GMT+13', Calendar::format($dt, 'zz'));
        $this->assertSame('GMT+13', Calendar::format($dt, 'zzz'));
        $this->assertSame('GMT+2', Calendar::format($dt4, 'z'));
        $this->assertSame('GMT+2', Calendar::format($dt5, 'z'));
        $this->assertSame('Fiji Summer Time', Calendar::format($dt, 'zzzz'));
        $this->assertSame('GMT+02:00', Calendar::format($dt4, 'zzzz'));
        $this->assertSame('GMT+02:00', Calendar::format($dt5, 'zzzz'));
        $this->assertSame('Ora legale delle Figi', Calendar::format($dt, 'zzzz', 'it'));
        $this->assertSame('GMT-01:02', Calendar::format(Calendar::toDateTime('10/Oct/2000:13:55:36 -0102'), 'zzzz'));
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
        $this->assertSame('Fiji Time', Calendar::format($dt, 'v'));
        $this->assertSame('GMT+02:00', Calendar::format($dt4, 'v'));
        $this->assertSame('GMT+02:00', Calendar::format($dt5, 'v'));
        $this->assertSame('Fiji Time', Calendar::format($dt, 'vvvv'));
        $this->assertSame('GMT+02:00', Calendar::format($dt4, 'vvvv'));
        $this->assertSame('GMT+02:00', Calendar::format($dt5, 'vvvv'));
        $this->assertSame('GMT+14:15', Calendar::format(Calendar::toDateTime('2000-01-01 11:12:13+14:15'), 'vvvv'));
        $this->assertSame('heure : Fidji', Calendar::format($dt, 'v', 'fr'));
        $this->assertSame('heure des îles Fidji', Calendar::format($dt, 'vvvv', 'fr'));
        // decodeTimezoneID
        $this->assertSame('unk', Calendar::format($dt, 'V'));
        $this->assertSame('Pacific/Fiji', Calendar::format($dt, 'VV'));
        $this->assertSame('Etc/GMT-2', Calendar::format($dt4, 'VV'));
        $this->assertSame('Fiji', Calendar::format($dt, 'VVV'));
        $this->assertSame('Fiji Time', Calendar::format($dt, 'VVVV'));
        $this->assertSame('GMT+02:00', Calendar::format($dt4, 'VVVV'));
        $this->assertSame('GMT+02:00', Calendar::format($dt5, 'VVVV'));
        $this->assertSame('heure : Fidji', Calendar::format($dt, 'VVVV', 'fr'));
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
        // Literal text
        $this->assertSame("2010'01", Calendar::format($dt, "yyyy''MM"));
        $this->assertSame("2010''01", Calendar::format($dt, "yyyy''''MM"));
        $this->assertSame("2010E'E01", Calendar::format($dt, "yyyy'E''E'MM"));
    }

    /**
     * @return array
     */
    public function providerDescribeInterval()
    {
        $now = new \DateTime('2017-11-01T16:18:44', new \DateTimeZone('Europe/Rome'));
        $before1 = clone $now;
        $before1->sub(new \DateInterval('P2Y4DT6H8M'));
        $before2 = clone $now;
        $before2->sub(new \DateInterval('P2Y3M4DT6H8M59S'));
        $before3 = clone $now;
        $before3->sub(new \DateInterval('P1Y3M4DT6H8M59S'));
        $nowTZ1 = clone $now;
        $nowTZ1->setTimezone(new \DateTimeZone('Pacific/Pago_Pago'));
        $nowTZ2 = clone $now;
        $nowTZ2->setTimezone(new \DateTimeZone('Pacific/Kiritimati'));

        return array(
            array('now', $now, $now, 1, 'short', 'en'),
            array('2 yrs', $now, $before1, 1, 'short', 'en'),
            array('2 years', $now, $before1, 1, 'long', 'en'),
            array('2y 4d', $now, $before1, 2, 'narrow', 'en'),
            array('2 years, 4 days, and 6 hours', $now, $before1, 3, 'long', 'en'),
            array('2 yrs, 4 days, 6 hr', $now, $before1, 3, 'short', 'en'),
            array('2y 4d 6h', $now, $before1, 3, 'narrow', 'en'),
            array('2 years, 4 days, 6 hours, and 8 minutes', $now, $before1, 4, 'long', 'en'),
            array('2 years and 3 months', $now, $before2, 2, 'long', 'en'),
            array('2 years, 3 months, 4 days, and 6 hours', $now, $before2, 4, 'long', 'en'),
            array('1 year', $now, $before3, 1, 'long', 'en'),
            array('2 anni e 3 mesi', $now, $before2, 2, 'long', 'it'),
            array('2 anni, 3 mesi, 4 giorni e 6 ore', $now, $before2, 4, 'long', 'it'),
            array('2 anni, 3 mesi, 4 giorni, 6 ore, 8 minuti e 59 secondi', $now, $before2, 99, 'long', 'it'),
            array('now', $nowTZ1, $nowTZ2, 1, 'short', 'en'),
        );
    }

    /**
     * Test describeInterval.
     *
     * @dataProvider providerDescribeInterval
     *
     * @param string $expected
     * @param \DateTime $dateEnd
     * @param \DateTime $dateStart
     * @param int $maxParts
     * @param string $width
     * @param string $locale
     */
    public function testDescribeInterval($expected, $dateEnd, $dateStart, $maxParts, $width, $locale)
    {
        $this->assertSame(
            $expected,
            Calendar::describeInterval($dateEnd, $dateStart, $maxParts, $width, $locale)
        );
    }

    /**
     * Test describeInterval.
     *
     * @dataProvider providerDescribeInterval
     */
    public function testDescribeInterval2()
    {
        $this->assertRegExp(
            '/^(now|1 second|\\d+ seconds)$/',
            Calendar::describeInterval(new \DateTime(), null, 1, 'long', 'en')
        );
    }

    public function testGetSortedWeekdays()
    {
        $this->assertSame(
            array(0, 1, 2, 3, 4, 5, 6),
            Calendar::getSortedWeekdays(null, 'en')
        );
        $this->assertSame(
            array(1, 2, 3, 4, 5, 6, 0),
            Calendar::getSortedWeekdays(null, 'it')
        );
        $this->assertSame(
            array(
                array('id' => 0, 'name' => 'Su'),
                array('id' => 1, 'name' => 'Mo'),
                array('id' => 2, 'name' => 'Tu'),
                array('id' => 3, 'name' => 'We'),
                array('id' => 4, 'name' => 'Th'),
                array('id' => 5, 'name' => 'Fr'),
                array('id' => 6, 'name' => 'Sa'),
            ),
            Calendar::getSortedWeekdays('short', 'en')
        );
        $this->assertSame(
            array(
                array('id' => 1, 'name' => 'lun'),
                array('id' => 2, 'name' => 'mar'),
                array('id' => 3, 'name' => 'mer'),
                array('id' => 4, 'name' => 'gio'),
                array('id' => 5, 'name' => 'ven'),
                array('id' => 6, 'name' => 'sab'),
                array('id' => 0, 'name' => 'dom'),
            ),
            Calendar::getSortedWeekdays('short', 'it')
        );
        $this->assertSame(
            array(
                array('id' => 1, 'name' => 'lun'),
                array('id' => 2, 'name' => 'mar'),
                array('id' => 3, 'name' => 'mer'),
                array('id' => 4, 'name' => 'gio'),
                array('id' => 5, 'name' => 'ven'),
                array('id' => 6, 'name' => 'sab'),
                array('id' => 0, 'name' => 'dom'),
            ),
            Calendar::getSortedWeekdays('short', 'it')
        );
    }

    /**
     * @return array
     */
    public function providerGetDeltaDays()
    {
        return array(
            array(0, array(new \DateTime())),
            array(1, array(new \DateTime('+1 days'))),
            array(5, array(new \DateTime('+4 days'), new \DateTime('-1 days'))),
            array(0, array(new \DateTime('now', new \DateTimeZone('Pacific/Pago_Pago')), new \DateTime('now', new \DateTimeZone('Pacific/Kiritimati')))),
        );
    }

    /**
     * Test getDeltaDays.
     *
     * @dataProvider providerGetDeltaDays
     *
     * @param int $expected
     * @param array $arguments
     */
    public function testGetDeltaDays($expected, $arguments)
    {
        $this->assertSame(
            $expected,
            call_user_func_array('\\Punic\\Calendar::getDeltaDays', $arguments)
        );
    }

    public function testFormatEx()
    {
        $this->assertSame(
            '2010',
            Calendar::formatEx('2010-12-31 23:59', 'y')
        );
        $this->assertSame(
            '2010',
            Calendar::formatEx('2010-01-01 00:00', 'y')
        );
    }

    /**
     * @return array
     */
    public function providerGetTimezonesAliases()
    {
        return array(
          array('Asmara', 'Africa/Asmara'),
          array('Atikokan', 'America/Atikokan'),
          array('Ho Chi Minh City', 'Asia/Ho_Chi_Minh'),
          array('Kathmandu', 'Asia/Kathmandu'),
          array('Kolkata', 'Asia/Kolkata'),
          array('Faroe', 'Atlantic/Faroe'),
          array('Chuuk', 'Pacific/Chuuk'),
          array('Pohnpei', 'Pacific/Pohnpei'),
          array('Buenos Aires', 'America/Argentina/Buenos_Aires'),
          array('Catamarca', 'America/Argentina/Catamarca'),
          array('Cordoba', 'America/Argentina/Cordoba'),
          array('Jujuy', 'America/Argentina/Jujuy'),
          array('Mendoza', 'America/Argentina/Mendoza'),
          array('Indianapolis', 'America/Indiana/Indianapolis'),
          array('Louisville', 'America/Kentucky/Louisville'),
          array('Unknown City', 'America/Not_Existing_TimeZone_Name'),
       );
    }

    /**
     * Test getTimezonesAliases.
     *
     * @dataProvider providerGetTimezonesAliases
     *
     * @param string $expected
     * @param string $phpTimezoneName
     */
    public function testGetTimezonesAliases($expected, $phpTimezoneName)
    {
        $this->assertSame(
            $expected,
            \Punic\Calendar::getTimezoneExemplarCity($phpTimezoneName, true, 'en')
        );
    }
}
