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

    public function testCountries()
    {
        $countries = Territory::getCountries();
        $this->assertArrayHasKey('CA', $countries);
        
        // this list isn't static, we assume that something between 240 and 280 countries is okay
        $this->assertLessThan(280, count($countries));
        $this->assertGreaterThan(240, count($countries));
    }
    
    public function testContinents()
    {
        $continents = Territory::getContinents();
        $this->assertContains('Africa', $continents);
        
        // this list isn't static, we assume that something between 3 and 7 continents is okay
        $this->assertLessThan(count($continents), 3);
        $this->assertGreaterThan(count($continents), 7);
    }
}
