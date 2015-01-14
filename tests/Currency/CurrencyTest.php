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
            array('en', 'USD', null, 'US Dollar', 'US$', '$', ''),
            array('it', 'USD', null, 'Dollaro Statunitense', 'US$', '$', ''),
            array('en', 'Invalid currency code', null, '', '', '', ''),
            array('de', 'RUB', null, 'Russischer Rubel', 'RUB', 'RUB', '₽'),
            array('en', 'USD', 0, 'US dollars', 'US$', '$', ''),
            array('en', 'USD', 1, 'US dollar', 'US$', '$', ''),
            array('en', 'USD', 2, 'US dollars', 'US$', '$', ''),
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
}
