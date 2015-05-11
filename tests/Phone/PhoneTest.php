<?php

use \Punic\Phone;

class PhoneTest extends PHPUnit_Framework_TestCase
{
    public function somePrefixes()
    {
        return array(
            array('US', '1'),
            array('CA', '1'),
            array('IT', '39'),
            array('DE', '49'),
            array('001', '388'),
        );
    }

    /**
     * @dataProvider somePrefixes
     */
    public function testGetPrefixesForTerritory($territoryCode, $prefix)
    {
        $this->assertContains(
            $prefix,
            Phone::getPrefixesForTerritory($territoryCode)
        );
    }

    /**
     * @dataProvider somePrefixes
     */
    public function testGetTerritoriesForPrefix($territoryCode, $prefix)
    {
        $this->assertContains(
            $territoryCode,
            Phone::getTerritoriesForPrefix($prefix)
        );
    }

    public function testGetMaxPrefixLength()
    {
        $maxLength = Phone::getMaxPrefixLength();
        $this->assertGreaterThanOrEqual(1, $maxLength);
        $this->assertLessThanOrEqual(6, $maxLength);
    }
}
