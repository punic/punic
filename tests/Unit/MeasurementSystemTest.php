<?php

namespace Punic\Test\Unit;

use Punic\Data;
use Punic\Test\TestCase;
use Punic\Unit;

class MeasurementSystemTest extends TestCase
{
    public function testIdentifiersConsistency()
    {
        $keys = null;
        foreach (Data::getAvailableLocales(true) as $locale) {
            $theseKeys = array_keys(Unit::getMeasurementSystems($locale));
            sort($theseKeys);
            if (null === $keys) {
                $keys = $theseKeys;
            } else {
                $this->assertSame($keys, $theseKeys);
            }
        }
        $this->assertNotNull($keys);
    }

    /**
     * @return array
     */
    public function provideGetMeasurementSystemFor()
    {
        return array(
            array('US', 'US'),
            array('IT', 'metric'),
            array('DE', 'metric'),
        );
    }

    /**
     * @dataProvider provideGetMeasurementSystemFor
     *
     * @param string $territoryCode
     * @param string $measurementSystemCode
     */
    public function testGetMeasurementSystemFor($territoryCode, $measurementSystemCode)
    {
        $this->assertSame(
            $measurementSystemCode,
            Unit::getMeasurementSystemFor($territoryCode)
        );
    }

    /**
     * @return array
     */
    public function provideGetCountriesWithMeasurementSystem()
    {
        return array(
            array('US', 'US', true),
            array('US', 'IT', false),
            array('metric', 'US', false),
            array('metric', 'IT', true),
        );
    }

    /**
     * @dataProvider provideGetCountriesWithMeasurementSystem
     *
     * @param string $measurementSystemCode
     * @param string $territoryCode
     * @param bool $territoryPresent
     */
    public function testGetCountriesWithMeasurementSystem($measurementSystemCode, $territoryCode, $territoryPresent)
    {
        $countries = Unit::getCountriesWithMeasurementSystem($measurementSystemCode);
        if ($territoryPresent) {
            $this->assertContains($territoryCode, $countries);
        } else {
            $this->assertNotContains($territoryCode, $countries);
        }
    }

    /**
     * @return array
     */
    public function provideGetPaperSizeFor()
    {
        return array(
            array('US', 'US-Letter'),
            array('IT', 'A4'),
            array('DE', 'A4'),
        );
    }

    /**
     * @dataProvider provideGetPaperSizeFor
     *
     * @param string $territoryCode
     * @param string $paperSize
     */
    public function testGetPaperSizeFor($territoryCode, $paperSize)
    {
        $this->assertSame(
            $paperSize,
            Unit::getPaperSizeFor($territoryCode)
        );
    }

    /**
     * @return array
     */
    public function provideGetCountriesWithPaperSize()
    {
        return array(
            array('US-Letter', 'US', true),
            array('US-Letter', 'IT', false),
            array('A4', 'US', false),
            array('A4', 'IT', true),
        );
    }

    /**
     * @dataProvider provideGetCountriesWithPaperSize
     *
     * @param string $paperSize
     * @param string $territoryCode
     * @param bool $territoryPresent
     */
    public function testGetCountriesWithPaperSize($paperSize, $territoryCode, $territoryPresent)
    {
        $countries = Unit::getCountriesWithPaperSize($paperSize);
        if ($territoryPresent) {
            $this->assertContains($territoryCode, $countries);
        } else {
            $this->assertNotContains($territoryCode, $countries);
        }
    }
}
