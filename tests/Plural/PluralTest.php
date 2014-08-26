<?php
use \Punic\Plural;

class PluralTest extends PHPUnit_Framework_TestCase
{
    public function providerCheckPlurals()
    {
        $testDataFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'dataFiles' . DIRECTORY_SEPARATOR . 'plurals.json';
        if (!is_file($testDataFile)) {
            throw new \Exception("Test data file not found: plurals.json");
        }
        $json = @file_get_contents($testDataFile);
        if ($json === false) {
            throw new \Exception("Test data file not readable: plurals.json");
        }
        $data = @json_decode($json, true);
        if (!is_array($data)) {
            throw new \Exception("Test data file not valid: plurals.json");
        }
        $parameters = array();
        foreach ($data as $language => $rules) {
            foreach ($rules as $rule => $values) {
                foreach ($values as $value) {
                    $parameters[] = array(
                        $rule,
                        array($value, $language)
                    );
                }
            }
        }

        return $parameters;
    }

    /**
     * test getPluralRule
     * expected boolean
     * @dataProvider providerCheckPlurals
     */
    public function testGetPluralRule($rule, $parameters)
    {
        $this->assertSame(
            $rule,
            call_user_func_array('\Punic\Plural::getPluralRule', $parameters)
        );
    }

}
