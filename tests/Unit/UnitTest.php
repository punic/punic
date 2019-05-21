<?php

namespace Punic\Test\Unit;

use Punic\Exception;
use Punic\Test\TestCase;
use Punic\Unit;

class UnitTest extends TestCase
{
    /**
     * @return array
     */
    public function provideFormat()
    {
        return array(
            array(
                '1 millisecond',
                array(1, 'millisecond', 'long', 'en'),
            ),
            array(
                '1 millisecond',
                array(1, 'duration/millisecond', 'long', 'en'),
            ),
            array(
                '2 milliseconds',
                array(2, 'millisecond', 'long', 'en'),
            ),
            array(
                '0 milliseconds',
                array(0, 'millisecond', 'long', 'en'),
            ),
            array(
                '0 milliseconde',
                array(0, 'millisecond', 'long', 'fr'),
            ),
            array(
                '1 milliseconde',
                array(1, 'millisecond', 'long', 'fr'),
            ),
            array(
                '2 millisecondes',
                array(2, 'millisecond', 'long', 'fr'),
            ),
            array(
                '2 ms',
                array(2, 'millisecond', 'short', 'en'),
            ),
            array(
                '2ms',
                array(2, 'millisecond', 'narrow', 'en'),
            ),
            array(
                '0.2ms',
                array(.2, 'millisecond', 'narrow', 'en'),
            ),
            array(
                '2.0ms',
                array('2.0', 'millisecond', 'narrow', 'en'),
            ),
            array(
                '2.0 milliseconds',
                array(2, 'millisecond', 'long,1', 'en'),
            ),
            array(
                '2.0 milliseconds',
                array(2., 'millisecond', 'long,1', 'en'),
            ),
            array(
                '2.0 milliseconds',
                array('2.', 'millisecond', 'long,1', 'en'),
            ),
            array(
                '2.0 milliseconds',
                array('2.0123', 'millisecond', 'long,1', 'en'),
            ),
            array(
                '2.0 ms',
                array('2.0123', 'millisecond', '1', 'en'),
            ),
            array(
                '2.0 ms',
                array('2.0123', 'millisecond', 1, 'en'),
            ),
            array(
                '2,0 millisecondi',
                array('2.0123', 'millisecond', 'long,1', 'it'),
            ),
        );
    }

    /**
     * test format.
     *
     * @dataProvider provideFormat
     *
     * @param string $result
     * @param array $parameters
     */
    public function testFormat($result, $parameters)
    {
        $this->assertContains(
            Unit::format($parameters[0], $parameters[1], $parameters[2], $parameters[3]),
            array($result, str_replace(' ', "\xC2\xA0", $result))
        );
    }

    public function testValueNotInListExceptionGetValue()
    {
        try {
            Unit::format(2, 'milisecond', 'does-not-exist');
        } catch (Exception\ValueNotInList $ex) {
            $this->assertSame('does-not-exist', $ex->getValue());
            $this->assertSame(array('long', 'short', 'narrow'), $ex->getAllowedValues());
        }
    }

    public function testValueNotInListException()
    {
        $this->setExpectedException('Punic\\Exception\\ValueNotInList');
        Unit::format(2, 'milisecond', 'does-not-exist');
    }

    public function testInvalidUnit()
    {
        $this->setExpectedException('Punic\\Exception\\ValueNotInList');
        Unit::format(2, 'invalid-unit');
    }

    /**
     * @return array
     */
    public function provideGetUnitData()
    {
        return array(
            array('en_US', 'duration/minute', 'long', array('minutes'), array('%1$s per minute')),
            array('en_US', 'duration/minute', 'short', array('mins'), array('%1$s/min')),
            array('en_US', 'duration/minute', 'narrow', array('min'), array('%1$s/min')),
            array('en_US', 'minute', 'long', array('minutes'), array('%1$s per minute')),
            array('en_US', 'minute', 'short', array('mins'), array('%1$s/min')),
            array('en_US', 'minute', 'narrow', array('min'), array('%1$s/min')),
            array('it', 'duration/minute', 'long', array('minuti'), array('%1$s al minuto')),
            array('it', 'duration/minute', 'short', array('min'), array('%1$s/min')),
            array('it', 'duration/minute', 'narrow', array('min'), array('%1$s/min')),
            array('it', 'minute', 'long', array('minuti'), array('%1$s al minuto')),
            array('it', 'minute', 'short', array('min'), array('%1$s/min')),
            array('it', 'minute', 'narrow', array('min'), array('%1$s/min')),
            array('en_US', 'length/millimeter', 'long', array('millimeters'), array('%1$s per millimeter')),
            array('en_US', 'length/millimeter', 'short', array('mm'), array('%1$s/mm')),
            array('en_US', 'length/millimeter', 'narrow', array('mm'), array('%1$s/mm')),
            array('it', 'length/millimeter', 'long', array('millimetri'), array('%1$s al millimetro')),
            array('it', 'length/millimeter', 'short', array('mm'), array('%1$s/mm')),
            array('it', 'length/millimeter', 'narrow', array('mm'), array('%1$s/mm')),
        );
    }

    /**
     * Test getName.
     *
     * @dataProvider provideGetUnitData
     *
     * @param string $locale
     * @param string $unit
     * @param string $width
     * @param string[] $expectedNames
     * @param string[] $expectedPers
     */
    public function testGetName($locale, $unit, $width, array $expectedNames, array $expectedPers)
    {
        $actual = Unit::getName($unit, $width, $locale);
        $this->assertContains($actual, $expectedNames, '', false, true, true);
    }

    /**
     * Test getPerFormat.
     *
     * @dataProvider provideGetUnitData
     *
     * @param string $locale
     * @param string $unit
     * @param string $width
     * @param string[] $expectedNames
     * @param string[] $expectedPers
     */
    public function testGetPerFormat($locale, $unit, $width, array $expectedNames, array $expectedPers)
    {
        $actual = Unit::getPerFormat($unit, $width, $locale);
        $this->assertContains($actual, $expectedPers, '', false, true, true);
    }

    public function testGetAvailableUnits()
    {
        $categorizedUnits = Unit::getAvailableUnits('it');
        $this->assertArrayHasKey('acceleration', $categorizedUnits);
        $this->assertContains('g-force', $categorizedUnits['acceleration']);
        $this->assertArrayHasKey('volume', $categorizedUnits);
        $this->assertContains('teaspoon', $categorizedUnits['volume']);
    }
}
