<?php

use Punic\Data;

class DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function testInvalidLocalesProvider()
    {
        return array(
            array('setFallbackLocale', array(''), '\\Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(null), '\\Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(true), '\\Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(false), '\\Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array(new \stdClass()), '\\Punic\\Exception\\InvalidLocale'),
            array('setFallbackLocale', array('invalid'), '\\Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(''), '\\Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(null), '\\Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(true), '\\Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(false), '\\Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array(new \stdClass()), '\\Punic\\Exception\\InvalidLocale'),
            array('setDefaultLocale', array('invalid'), '\\Punic\\Exception\\InvalidLocale'),
        );
    }

    /**
     * @dataProvider testInvalidLocalesProvider
     *
     * @param string $method
     * @param array $parameters
     * @param string $exception
     */
    public function testInvalidLocales($method, $parameters, $exception)
    {
        $this->setExpectedException($exception);
        call_user_func_array(array('\\Punic\\Data', $method), $parameters);
    }

    public function testInvalidLocaleGet()
    {
        try {
            Data::setFallbackLocale('invalid');
        } catch (\Punic\Exception\InvalidLocale $ex) {
            $this->assertSame('invalid', $ex->getLocale());
        }
    }

    public function testDefaultLocale()
    {
        Data::setDefaultLocale('it');
        $this->assertSame('it', \Punic\Data::getDefaultLocale());
    }

    public function testDefaultLanguage()
    {
        Data::setDefaultLocale('de_DE');
        $this->assertSame('de', \Punic\Data::getDefaultLanguage());
    }

    public function testFallbackLocale()
    {
        Data::setFallbackLocale('it');
        $this->assertSame('it', \Punic\Data::getFallbackLocale());
    }

    public function testFallbackLanguage()
    {
        Data::setFallbackLocale('de_DE');
        $this->assertSame('de', \Punic\Data::getFallbackLanguage());
    }

    /**
     * @return array
     */
    public function providerInvalidDataFile()
    {
        return array(
            array('\\Punic\Exception\InvalidDataFile', 'get', true),
            array('\\Punic\Exception\InvalidDataFile', 'get', array()),
            array('\\Punic\Exception\InvalidDataFile', 'get', ''),
            array('\\Punic\Exception\InvalidDataFile', 'get', '../x'),
            array('\\Punic\Exception\InvalidDataFile', 'get', '*'),
            array('\\Punic\Exception\DataFileNotFound', 'get', 'invalid-data-file'),
            array('\\Punic\Exception\InvalidDataFile', 'getGeneric', true),
            array('\\Punic\Exception\InvalidDataFile', 'getGeneric', array()),
            array('\\Punic\Exception\InvalidDataFile', 'getGeneric', ''),
            array('\\Punic\Exception\InvalidDataFile', 'getGeneric', '../x'),
            array('\\Punic\Exception\InvalidDataFile', 'getGeneric', '*'),
            array('\\Punic\Exception\DataFileNotFound', 'getGeneric', 'invalid-data-file'),
        );
    }

    /**
     * @dataProvider providerInvalidDataFile
     *
     * @param string $exception
     * @param string $method
     * @param string|mixed $dataFileID
     */
    public function testInvalidDataFile($exception, $method, $dataFileID)
    {
        $this->setExpectedException($exception);
        call_user_func(array('\\Punic\\Data', $method), $dataFileID);
    }

    public function testGetAvailableLocales()
    {
        $locales = Data::getAvailableLocales();
        // this list isn't static, we assume that something between 1 and 320 locales is okay
        $this->assertLessThan(2000, count($locales));
        $this->assertGreaterThan(1, count($locales));
    }

    /**
     * @return array
     */
    public function providerGuessFullLocale()
    {
        return array(
            array('en-Latn-US', array('en')),
            array('it-Latn-IT', array('it')),
            array('de-Latn-DE', array('de')),
            array('de-Cyrl-DE', array('de', 'Cyrl')),
            array('az-Cyrl-AZ', array('az', 'Cyrl')),
        );
    }

    /**
     * @dataProvider providerGuessFullLocale
     *
     * @param string $result
     * @param array $parameters
     */
    public function testGuessFullLocale($result, $parameters)
    {
        $locale = call_user_func_array(array('\Punic\Data', 'guessFullLocale'), $parameters);
        $this->assertSame($result, $locale);
    }

    public function testGuessFullDefaultLocale()
    {
        Data::setDefaultLocale('de_DE');
        $locale = Data::guessFullLocale();
        $this->assertSame('de-Latn-DE', $locale);
    }

    /**
     * @return array
     */
    public function providerGetTerritory()
    {
        return array(
            array('US', array('en_US')),
            array('IT', array('it_IT')),
            array('DE', array('de_DE')),
            array('CH', array('de_CH')),
            array('AZ', array('az_Latn_AZ')),
            array('AZ', array('az')),
        );
    }

    /**
     * @dataProvider providerGetTerritory
     *
     * @param string $result
     * @param array $parameters
     */
    public function testGetTerritory($result, $parameters)
    {
        $locale = call_user_func_array(array('\Punic\Data', 'getTerritory'), $parameters);
        $this->assertSame($result, $locale);
    }
}
