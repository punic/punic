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
        try
        {
            Data::setFallbackLocale('invalid');
        }
        catch (\Punic\Exception\InvalidLocale $ex)
        {
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
    
}
