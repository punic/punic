<?php

class MeasurementSystemTest extends PHPUnit_Framework_TestCase
{
    public function testIdentifiersConsistency()
    {
        $keys = null;
        foreach (\Punic\Data::getAvailableLocales(true) as $locale) {
            $theseKeys = array_keys(\Punic\Unit::getMeasurementSystems($locale));
            sort($theseKeys);
            if (is_null($keys)) {
                $keys = $theseKeys;
            } else {
                $this->assertSame($keys, $theseKeys);
            }
        }
        $this->assertNotNull($keys);
    }

    public function providerGetMeasurementSystemFor()
    {
        return array(
            array('US', 'US'),
            array('IT', 'metric'),
            array('DE', 'metric'),
        );
    }

    /**
     * @dataProvider providerGetMeasurementSystemFor
     */
    public function testGetMeasurementSystemFor($territoryCode, $measurementSystemCode)
    {
        $this->assertSame(
            $measurementSystemCode,
            \Punic\Unit::getMeasurementSystemFor($territoryCode)
        );
    }

    public function providerGetCountriesWithMeasurementSystem()
    {
        return array(
            array('US', 'US', true),
            array('US', 'IT', false),
            array('metric', 'US', false),
            array('metric', 'IT', true),
        );
    }

    /**
     * @dataProvider providerGetCountriesWithMeasurementSystem
     */
    public function testGetCountriesWithMeasurementSystem($measurementSystemCode, $territoryCode, $territoryPresent)
    {
        $countries = \Punic\Unit::getCountriesWithMeasurementSystem($measurementSystemCode);
        if ($territoryPresent) {
            $this->assertContains($territoryCode, $countries);
        } else {
            $this->assertNotContains($territoryCode, $countries);
        }
    }

    public function providerGetPaperSizeFor()
    {
        return array(
            array('US', 'US-Letter'),
            array('IT', 'A4'),
            array('DE', 'A4'),
        );
    }

    /**
     * @dataProvider providerGetPaperSizeFor
     */
    public function testGetPaperSizeFor($territoryCode, $paperSize)
    {
        $this->assertSame(
            $paperSize,
            \Punic\Unit::getPaperSizeFor($territoryCode)
        );
    }

    public function providerGetCountriesWithPaperSize()
    {
        return array(
            array('US-Letter', 'US', true),
            array('US-Letter', 'IT', false),
            array('A4', 'US', false),
            array('A4', 'IT', true),
        );
    }

    /**
     * @dataProvider providerGetCountriesWithPaperSize
     */
    public function testGetCountriesWithPaperSize($paperSize, $territoryCode, $territoryPresent)
    {
        $countries = \Punic\Unit::getCountriesWithPaperSize($paperSize);
        if ($territoryPresent) {
            $this->assertContains($territoryCode, $countries);
        } else {
            $this->assertNotContains($territoryCode, $countries);
        }
    }
}
