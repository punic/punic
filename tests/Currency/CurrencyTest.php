<?php

class CurrencyTest extends PHPUnit_Framework_TestCase
{
    public function testAvailability()
    {
        $allCurrencyCodes = null;
        foreach (\Punic\Data::getAvailableLocales(true) as $locale) {
            $theseCurrencyCodes = array_keys(\Punic\Currency::getAllCurrencies(true, true, $locale));
            sort($theseCurrencyCodes);
            if (is_null($allCurrencyCodes)) {
                $allCurrencyCodes = $theseCurrencyCodes;
            } else {
                $this->assertSame($allCurrencyCodes, $theseCurrencyCodes);
            }
        }
        $this->assertNotNull($allCurrencyCodes);
    }

    public function providerGetInfo()
    {
        return array(
            array('en', 'USD', null, 'US Dollar', '$', '$', ''),
            array('it', 'USD', null, 'dollaro statunitense', 'US$', '$', ''),
            array('en', 'Invalid currency code', null, '', '', '', ''),
            array('de', 'ARS', null, 'Argentinischer Peso', 'ARS', '$', ''),
            array('en', 'USD', 0, 'US dollars', '$', '$', ''),
            array('en', 'USD', 1, 'US dollar', '$', '$', ''),
            array('en', 'USD', 2, 'US dollars', '$', '$', ''),
        );
    }

    /**
     * @dataProvider providerGetInfo
     */
    public function testGetInfo($locale, $currencyCode, $quantity, $currencyName, $currencySymbol, $currencySymbolNarrow, $currencySymbolAlternative)
    {
        $this->assertSame($currencyName, \Punic\Currency::getName($currencyCode, $quantity, $locale), 'Error getting name');
        $this->assertSame($currencySymbol, \Punic\Currency::getSymbol($currencyCode, '', $locale), 'Error getting standard symbol');
        $this->assertSame($currencySymbolNarrow, \Punic\Currency::getSymbol($currencyCode, 'narrow', $locale), 'Error getting narrow symbol');
        $this->assertSame($currencySymbolAlternative, \Punic\Currency::getSymbol($currencyCode, 'alt', $locale), 'Error getting alternative symbol');
    }

    public function providerGetCurrencyForTerritory()
    {
        return array(
            array('US', 'USD'),
            array('IT', 'EUR'),
            array('DE', 'EUR'),
            array('Invalid territory code', ''),
        );
    }

    /**
     * @dataProvider providerGetCurrencyForTerritory
     */
    public function testGetCurrencyForTerritory($territoryCode, $currencyCode)
    {
        $this->assertSame($currencyCode, \Punic\Currency::getCurrencyForTerritory($territoryCode));
    }

    public function testGetAllCurrencies()
    {
        $currencies = \Punic\Currency::getAllCurrencies();
        $this->assertArrayHasKey('USD', $currencies);
        $this->assertArrayHasKey('CHF', $currencies);

        // this list isn't static, we assume that something between 140 and 170 currenciess is okay
        $this->assertLessThan(170, count($currencies));
        $this->assertGreaterThan(140, count($currencies));
    }
}
