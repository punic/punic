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

    public function testContinentsAndCountries()
    {
        $continentsAndCountries = Territory::getContinentsAndCountries();

        // this list isn't static, we assume that something between 3 and 7 continents is okay
        $this->assertLessThan(count($continentsAndCountries), 3);
        $this->assertGreaterThan(count($continentsAndCountries), 7);
    }

    public function testInvalidTerritoryTypeException()
    {
        $this->setExpectedException('\\Punic\\Exception\\BadArgumentType');
        $list = Territory::getList('a');
    }

    public function testTerritoriesWithInfo()
    {
        $territories = Territory::getTerritoriesWithInfo();
        $this->assertContains('US', $territories);
        $this->assertContains('IT', $territories);
        $this->assertContains('DE', $territories);
    }

    public function testLanguages()
    {
        $enFound = false;
        $esFound = false;
        foreach (Territory::getLanguages('US') as $language) {
            switch ($language['id']) {
                case 'en':
                    $enFound = true;
                    break;
                case 'es':
                      $esFound = true;
                      break;
            }
        }
        $this->assertTrue($enFound);
        $this->assertTrue($esFound);
    }

    public function testPopulation()
    {
        $this->assertNull(Territory::getPopulation('Invalid territory ID'));
        $us = Territory::getPopulation('US');
        $this->assertNotNull($us);
        $this->assertGreaterThanOrEqual(200000000, $us);
        $this->assertLessThan(600000000, $us);
        $cn = Territory::getPopulation('CN');
        $this->assertNotNull($cn);
        $this->assertGreaterThanOrEqual(1000000000, $cn);
        $this->assertLessThan(3000000000, $cn);
        $va = Territory::getPopulation('VA');
        $this->assertNotNull($va);
        $this->assertGreaterThanOrEqual(100, $va);
        $this->assertLessThan(2500, $va);
    }

    public function testLiteracyLevel()
    {
        $this->assertNull(Territory::getLiteracyLevel('Invalid territory ID'));
        $us = Territory::getLiteracyLevel('US');
        $this->assertNotNull($us);
        $this->assertGreaterThanOrEqual(50, $us);
        $this->assertLessThanOrEqual(100, $us);
        $cn = Territory::getLiteracyLevel('CN');
        $this->assertNotNull($cn);
        $this->assertGreaterThanOrEqual(50, $cn);
        $this->assertLessThanOrEqual(100, $cn);
        $va = Territory::getLiteracyLevel('VA');
        $this->assertNotNull($va);
        $this->assertGreaterThanOrEqual(50, $va);
        $this->assertLessThanOrEqual(100, $va);
    }

    public function testGrossDomesticProduct()
    {
        $this->assertNull(Territory::getGrossDomesticProduct('Invalid territory ID'));
        $us = Territory::getGrossDomesticProduct('US');
        $this->assertNotNull($us);
        $this->assertGreaterThanOrEqual(1000000000000, $us);
        $this->assertLessThanOrEqual(100000000000000, $us);
        $cn = Territory::getGrossDomesticProduct('CN');
        $this->assertNotNull($cn);
        $this->assertGreaterThanOrEqual(1000000000000, $cn);
        $this->assertLessThanOrEqual(100000000000000, $cn);
        $va = Territory::getGrossDomesticProduct('VA');
        $this->assertNotNull($va);
        $this->assertGreaterThanOrEqual(1000000, $va);
        $this->assertLessThanOrEqual(10000000000, $va);
    }

    public function testTerritoriesForLanguage()
    {
        $this->assertEmpty(Territory::getTerritoriesForLanguage('fake'));
        $us = Territory::getTerritoriesForLanguage('en');
        $this->assertSame('US', $us[0]);
        $this->assertContains('GB', $us);
        $it = Territory::getTerritoriesForLanguage('it');
        $this->assertSame('IT', $it[0]);
        $this->assertContains('CH', $it);
        $this->assertContains('SM', $it);
        $this->assertContains('VA', $it);
    }
}
