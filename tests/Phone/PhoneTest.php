<?php

namespace Punic\Test\Phone;

use Punic\Phone;
use Punic\Test\TestCase;

class PhoneTest extends TestCase
{
    /**
     * @return array
     */
    public function providePrefixes()
    {
        return array(
            array('US', '1'),
            array('CA', '1'),
            array('IT', '39'),
            array('DE', '49'),
            array('001', '800'),
        );
    }

    /**
     * @dataProvider providePrefixes
     *
     * @param string $territoryCode
     * @param string $prefix
     */
    public function testGetPrefixesForTerritory($territoryCode, $prefix)
    {
        $this->assertContains(
            $prefix,
            Phone::getPrefixesForTerritory($territoryCode)
        );
    }

    /**
     * @dataProvider providePrefixes
     *
     * @param string $territoryCode
     * @param string $prefix
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
