<?php
use \Punic\Territory;

class TerritoryTest extends PHPUnit_Framework_TestCase
{

    public function providerGetName()
    {
        return array(
            array('United States', 'US', 'en'),
            array('Stati Uniti', 'US', 'it'),
            array('Italy', 'IT', 'en'),
            array('Italia', 'IT', 'it'),
        );
    }

    /**
     * test getName
     * @dataProvider providerGetName
     */
    public function testGetName($result, $territoryCode, $forLocale)
    {
        $this->assertSame(
            $result,
            Territory::getName($territoryCode, $forLocale)
        );
    }

}
