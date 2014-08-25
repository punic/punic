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

    public function testDefaultLocale()
    {
        \Punic\Data::setFallbackLocale('it');
    }

}
