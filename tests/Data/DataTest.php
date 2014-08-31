<?php
use \Punic\Data;

class DataTest extends PHPUnit_Framework_TestCase
{

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
        \Punic\Data::setDefaultLocale('it');
        $this->assertSame('it', \Punic\Data::getDefaultLocale());
    }

    public function testDefaultLanguage()
    {
        \Punic\Data::setDefaultLocale('de_DE');
        $this->assertSame('de', \Punic\Data::getDefaultLanguage());
    }

    public function testFallbackLocale()
    {
        \Punic\Data::setFallbackLocale('it');
        $this->assertSame('it', \Punic\Data::getFallbackLocale());
    }

    public function testFallbackLanguage()
    {
        \Punic\Data::setFallbackLocale('de_DE');
        $this->assertSame('de', \Punic\Data::getFallbackLanguage());
    }

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
     */
    public function testInvalidDataFile($exception, $method, $dataFileID)
    {
        $this->setExpectedException($exception);
        call_user_func(array('\\Punic\\Data', $method), $dataFileID);
    }

}
