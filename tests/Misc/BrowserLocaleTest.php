<?php
use \Punic\Misc;

class BrowserLocaleTest extends PHPUnit_Framework_TestCase
{
    public function testBrowserLocales()
    {
        @putenv('HTTP_ACCEPT_LANGUAGE=en');
        $locales = Misc::getBrowserLocales(true);
        $this->assertSame(array('en' => 1), $locales);
        @putenv('HTTP_ACCEPT_LANGUAGE=');
    }

    public function testBrowserLocalesServer()
    {
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
        $locales = Misc::getBrowserLocales(true);
        $this->assertSame(array('en' => 1), $locales);        
    }

    public function providerParseHttpAcceptLanguage()
    {
        return array(
            array('helloworld', array()),
            array('en', array('en' => 1)),
            array('EN-invalidTerritoryCode', array('en' => 1)),
            array('en_US', array('en-US' => 1)),
            array('en_US-GB', array('en-US' => 1, 'en-GB' => 1)),
            array('zh-hans', array('zh-Hans' => 1)),
            array('zh-hans-invalid', array('zh-Hans' => 1)),
            array('En-invalidTerritoryCode, zh-hAnS-invalidTerritoryCode', array('en' => 1, 'zh-Hans' => 1)),
            array('en-us,en;q=0.8,es-cl;q=0.5,zh-cn;q=0.3', array('en-US' => 1, 'en' => 0.8, 'es-CL' => 0.5, 'zh-CN' => 0.3)),
            array('helloworld,en-US;q=0.8,en;q=0.6,de-CH;q=0.4,de;q=0.2', array('en-US' => 0.8, 'en' => 0.6, 'de-CH' => 0.4, 'de' => 0.2)),
            array(' it ;  q = 0.1 ', array('it' => 0.1)),
        );
    }

    /**
     * @dataProvider providerParseHttpAcceptLanguage
     */
    public function testParseHttpAcceptLanguage($httpAcceptLanguages, $expected)
    {
        $keys = array_keys($expected);
        sort($keys);
        $parsed = Misc::parseHttpAcceptLanguage($httpAcceptLanguages);
        $parsedKeys = array_keys($parsed);
        sort($parsedKeys);
        $this->assertEquals($keys, $parsedKeys);
        foreach ($keys as $key) {
            $this->assertEquals($expected[$key], $parsed[$key]);
        }
    }
}
