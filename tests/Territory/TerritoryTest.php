<?php

namespace Punic\Test\Territory;

use Punic\Territory;
use Punic\Test\TestCase;

class TerritoryTest extends TestCase
{
    /**
     * @return array
     */
    public function provideGetName()
    {
        return array(
            array('United States', 'US', 'en'),
            array('Stati Uniti', 'US', 'it'),
            array('Italy', 'IT', 'en'),
            array('Italy', 'it', 'en'),
            array('Rome', 'itrm', 'en'),
            array('provincia di Roma', 'itrm', 'it'),
        );
    }

    /**
     * test getName.
     *
     * @dataProvider provideGetName
     *
     * @param string $result
     * @param string $territoryCode
     * @param string $forLocale
     */
    public function testGetName($result, $territoryCode, $forLocale)
    {
        $this->assertSame(
            $result,
            Territory::getName($territoryCode, $forLocale)
        );
    }

    /**
     * @return array
     */
    public function provideGetCode()
    {
        return array(
            array('USA', 'US', 'alpha3'),
            array('840', 'US', 'numeric'),
            array('US', 'US', 'fips10'),
            array('AU', 'AT', 'fips10'),
            array('DGA', 'DG', 'alpha3'),
            array('', 'EA', 'alpha3'),
            array('', 'DG', 'numeric'),
            array('', 'FOO', 'alpha3'),
        );
    }

    /**
     * test getCode.
     *
     * @dataProvider provideGetCode
     *
     * @param string $result
     * @param string $territoryCode
     * @param string $type
     */
    public function testGetCode($result, $territoryCode, $type)
    {
        $this->assertSame(
            $result,
            Territory::getCode($territoryCode, $type)
        );
    }

    public function testGetCodeException()
    {
        $this->setExpectedException('Punic\\Exception\\ValueNotInList');
        Territory::getCode('DE', 'foo');
    }

    /**
     * @return array
     */
    public function provideGetByCode()
    {
        return array(
            array('US', 'USA', 'alpha3'),
            array('US', '840', 'numeric'),
            array('US', 840, 'numeric'),
            array('US', 'US', 'fips10'),
            array('AT', 'AU', 'fips10'),
            array('DG', 'DGA', 'alpha3'),
        );
    }

    /**
     * test getByCode.
     *
     * @dataProvider provideGetByCode
     *
     * @param string $result
     * @param string $territoryCode
     * @param string $type
     * @param mixed $code
     */
    public function testGetByCode($result, $code, $type)
    {
        $this->assertSame(
            $result,
            Territory::getByCode($code, $type)
        );
    }

    public function testGetByCodeException()
    {
        $this->setExpectedException('Punic\\Exception\\ValueNotInList');
        Territory::getByCode('666', 'foo');
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
        $this->setExpectedException('Punic\\Exception\\BadArgumentType');
        Territory::getList('a');
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
        $en = Territory::getTerritoriesForLanguage('en', 0);
        $this->assertSame('US', $en[0]);
        $this->assertContains('GB', $en);
        $this->assertContains('IT', $en);
        $enThreshold = Territory::getTerritoriesForLanguage('en', 80);
        $this->assertSame('US', $enThreshold[0]);
        $this->assertNotContains('IT', $enThreshold);
        $it = Territory::getTerritoriesForLanguage('it');
        $this->assertSame('IT', $it[0]);
        $this->assertContains('CH', $it);
        $this->assertContains('SM', $it);
        $this->assertContains('VA', $it);
    }

    /**
     * @return array
     */
    public function provideGetParentTerritoryCode()
    {
        return array(
            array(/*World*/'001', /*Nothing*/''),
            array(/*Europe*/'150', /*World*/'001'),
            array(/*Southern Europe*/'039', /*Europe*/'150'),
            array(/*Italy*/'IT', /*Northern Europe*/'039'),
            array(/*Italy*/'it', /*Northern Europe*/'039'),
            array(/*Lazio*/'it62', /*Italy*/'IT'),
            array(/*Rome*/'itrm', /*Lazio*/'it62'),
        );
    }

    /**
     * @dataProvider provideGetParentTerritoryCode
     *
     * @param string $child
     * @param string $parent
     */
    public function testGetParentTerritoryCode($child, $parent)
    {
        $this->assertSame(
            $parent,
            Territory::getParentTerritoryCode($child)
        );
    }

    /**
     * @return array
     */
    public function provideGetChildTerritoryCodes()
    {
        return array(
            array(/*World*/'001', false, false, /*Europe*/'150', true),
            array(/*World*/'001', false, false, 'IT', false),
            array(/*World*/'001', true, false, /*Europe*/'150', false),
            array(/*World*/'001', true, false, 'IT', true),
            array(/*World*/'001', true, true, 'IT', false),
            array(/*World*/'001', true, true, 'it62', false),
            array(/*World*/'001', true, true, 'itrm', true),
            array(/*Italy*/'IT', false, false, 'it62', false),
            array(/*Italy*/'IT', false, false, 'itrm', false),
            array(/*Italy*/'IT', false, true, 'it62', true),
            array(/*Italy*/'it', false, true, 'it62', true),
            array(/*Italy*/'IT', false, true, 'itrm', false),
            array(/*Italy*/'IT', true, true, 'it62', false),
            array(/*Italy*/'IT', true, true, 'itrm', true),
            array(/*Lazio*/'it62', true, false, 'itrm', true),
            array(/*Lazio*/'it62', true, true, 'itrm', true),
        );
    }

    /**
     * @dataProvider provideGetChildTerritoryCodes
     *
     * @param string $parentTerritoryCode
     * @param bool $expandSubGroups
     * @param bool $expandSubdivisions
     * @param string $childTerritoryCode
     * @param bool $childIncluded
     */
    public function testGetChildTerritoryCodes($parentTerritoryCode, $expandSubGroups, $expandSubdivisions, $childTerritoryCode, $childIncluded)
    {
        $children = Territory::getChildTerritoryCodes($parentTerritoryCode, $expandSubGroups, $expandSubdivisions);
        if ($childIncluded) {
            $this->assertContains($childTerritoryCode, $children);
        } else {
            $this->assertNotContains($childTerritoryCode, $children);
        }
    }

    public function testSorting()
    {
        $countries = Territory::getCountries('de_DE');
        $countryKeys = array_keys($countries);
        $indexCyprus = array_search(array_search('Zypern', $countries), $countryKeys);
        $indexAustria = array_search(array_search('Österreich', $countries), $countryKeys);

        $this->assertGreaterThan($indexAustria, $indexCyprus, 'Österreich was not listed before Zypern');
    }
}
