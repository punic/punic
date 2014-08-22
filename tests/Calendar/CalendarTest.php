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
}
