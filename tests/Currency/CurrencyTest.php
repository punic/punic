<?php

class CurrencyTest extends PHPUnit_Framework_TestCase
{
    public function testAvailability()
    {
        $allCurrencyCodes = null;
        foreach (\Punic\Data::getAvailableLocales(true) as $locale) {
            $theseCurrencyCodes = array_keys(\Punic\Currency::getAllCurrencies(true, true, $locale));
            sort($theseCurrencyCodes);
            if (null === $allCurrencyCodes) {
                $allCurrencyCodes = $theseCurrencyCodes;
            } else {
                $this->assertSame($allCurrencyCodes, $theseCurrencyCodes);
            }
        }
        $this->assertNotNull($allCurrencyCodes);
    }

    /**
     * @return array
     */
    public function providerGetInfo()
    {
        return array(
            array('en', 'USD', null, 'US Dollar', '$', '$', '', 840),
            array('it', 'USD', null, 'dollaro statunitense', 'USD', '$', '', 840),
            array('en', 'Invalid currency code', null, '', '', '', '', ''),
            array('de', 'ARS', null, 'Argentinischer Peso', 'ARS', '$', '', 32),
            array('en', 'USD', 0, 'US dollars', '$', '$', '', 840),
            array('en', 'USD', 1, 'US dollar', '$', '$', '', 840),
            array('en', 'USD', 2, 'US dollars', '$', '$', '', 840),
            array('en', 'USD', 'one', 'US dollar', '$', '$', '', 840),
            array('en', 'USD', 'many', 'US dollars', '$', '$', '', 840),
        );
    }

    /**
     * @dataProvider providerGetInfo
     *
     * @param string $locale
     * @param string $currencyCode
     * @param int|null $quantity
     * @param string $currencyName
     * @param string $currencySymbol
     * @param string $currencySymbolNarrow
     * @param string $currencySymbolAlternative
     * @param mixed $iso4217
     */
    public function testGetInfo($locale, $currencyCode, $quantity, $currencyName, $currencySymbol, $currencySymbolNarrow, $currencySymbolAlternative, $iso4217)
    {
        $this->assertSame($currencyName, \Punic\Currency::getName($currencyCode, $quantity, $locale), 'Error getting name');
        $this->assertSame($currencySymbol, \Punic\Currency::getSymbol($currencyCode, '', $locale), 'Error getting standard symbol');
        $this->assertSame($currencySymbolNarrow, \Punic\Currency::getSymbol($currencyCode, 'narrow', $locale), 'Error getting narrow symbol');
        $this->assertSame($currencySymbolAlternative, \Punic\Currency::getSymbol($currencyCode, 'alt', $locale), 'Error getting alternative symbol');
        $this->assertSame($iso4217, \Punic\Currency::getCode($currencyCode), 'Error getting code');
    }

    public function testGetByCode()
    {
        $this->assertSame('DKK', \Punic\Currency::getByCode(208));
        $this->assertSame('DKK', \Punic\Currency::getByCode('208'));
        $this->assertSame('', \Punic\Currency::getByCode(666));
    }

    /**
     * @return array
     */
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
     *
     * @param string $territoryCode
     * @param string $currencyCode
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
