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
}
