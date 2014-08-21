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

    /**
     * test convertPhpToIso
     * expected boolean
     */
    public function testConvertPhpToIso()
    {
        $this->assertSame('dd', Calendar::convertPhpToIsoFormat('d'));
        $this->assertSame('EE', Calendar::convertPhpToIsoFormat('D'));
        $this->assertSame('d', Calendar::convertPhpToIsoFormat('j'));
        $this->assertSame('EEEE', Calendar::convertPhpToIsoFormat('l'));
        $this->assertSame('eee', Calendar::convertPhpToIsoFormat('N'));
        $this->assertSame('SS', Calendar::convertPhpToIsoFormat('S'));
        $this->assertSame('e', Calendar::convertPhpToIsoFormat('w'));
        $this->assertSame('D', Calendar::convertPhpToIsoFormat('z'));
        $this->assertSame('ww', Calendar::convertPhpToIsoFormat('W'));
        $this->assertSame('MMMM', Calendar::convertPhpToIsoFormat('F'));
        $this->assertSame('MM', Calendar::convertPhpToIsoFormat('m'));
        $this->assertSame('MMM', Calendar::convertPhpToIsoFormat('M'));
        $this->assertSame('M', Calendar::convertPhpToIsoFormat('n'));
        $this->assertSame('ddd', Calendar::convertPhpToIsoFormat('t'));
        $this->assertSame('l', Calendar::convertPhpToIsoFormat('L'));
        $this->assertSame('YYYY', Calendar::convertPhpToIsoFormat('o'));
        $this->assertSame('yyyy', Calendar::convertPhpToIsoFormat('Y'));
        $this->assertSame('yy', Calendar::convertPhpToIsoFormat('y'));
        $this->assertSame('a', Calendar::convertPhpToIsoFormat('a'));
        $this->assertSame('a', Calendar::convertPhpToIsoFormat('A'));
        $this->assertSame('B', Calendar::convertPhpToIsoFormat('B'));
        $this->assertSame('h', Calendar::convertPhpToIsoFormat('g'));
        $this->assertSame('H', Calendar::convertPhpToIsoFormat('G'));
        $this->assertSame('hh', Calendar::convertPhpToIsoFormat('h'));
        $this->assertSame('HH', Calendar::convertPhpToIsoFormat('H'));
        $this->assertSame('mm', Calendar::convertPhpToIsoFormat('i'));
        $this->assertSame('ss', Calendar::convertPhpToIsoFormat('s'));
        $this->assertSame('zzzz', Calendar::convertPhpToIsoFormat('e'));
        $this->assertSame('I', Calendar::convertPhpToIsoFormat('I'));
        $this->assertSame('Z', Calendar::convertPhpToIsoFormat('O'));
        $this->assertSame('ZZZZ', Calendar::convertPhpToIsoFormat('P'));
        $this->assertSame('z', Calendar::convertPhpToIsoFormat('T'));
        $this->assertSame('X', Calendar::convertPhpToIsoFormat('Z'));
        $this->assertSame('yyyy-MM-ddTHH:mm:ssZZZZ', Calendar::convertPhpToIsoFormat('c'));
        $this->assertSame('r', Calendar::convertPhpToIsoFormat('r'));
        $this->assertSame('U', Calendar::convertPhpToIsoFormat('U'));
        $this->assertSame('HHmmss', Calendar::convertPhpToIsoFormat('His'));
        $this->assertSame("dd MMMM yyyy 'alle' H:mm:ss", Calendar::convertPhpToIsoFormat('d F Y \a\l\l\e G:i:s'));
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
