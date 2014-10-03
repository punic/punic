<?php
use \Punic\Plural;

class PluralTest extends PHPUnit_Framework_TestCase
{

    protected static function joinPluralRules($rules)
    {
        usort($rules, function ($a, $b) {
            foreach (array('zero', 'one', 'two', 'few', 'many', 'other') as $pr) {
                if ($a == $pr) {
                    return -1;
                }
                if ($b == $pr) {
                    return 1;
                }
            }

            return 0;
        });

        return implode(', ', $rules);
    }

    protected static function loadPluralRulesTestData()
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

        return $data;
    }

    public function providerGetRules()
    {
        $data = static::loadPluralRulesTestData();
        $parameters = array();
        foreach ($data as $language => $languageTest) {
            switch ($language) {
                case 'root':
                    // The test data for root is incomplete in the source
                    $rules = array('one', 'other');
                    break;
                default:
                    $rules = array_keys($languageTest);
                    break;
            }
            $parameters[] = array(
                static::joinPluralRules($rules),
                $language
            );
        }

        return $parameters;
    }

    /**
     * test getRules
     * expected boolean
     * @dataProvider providerGetRules
     */
    public function testGetRules($rules, $language)
    {
        $this->assertSame(
            $rules,
            static::joinPluralRules(\Punic\Plural::getRules($language))
        );
    }

    public function providerGetRule()
    {
        $data = static::loadPluralRulesTestData();
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

		// add custom rules to test some edge cases
		$rules = array(
			array('other', array(0, 'en')),
			array('one', array(1, 'en')),
			array('one', array(1.0, 'en')),
			array('other', array(1.1, 'en')),
			array('one', array('1', 'en')),
			array('other', array('1.0', 'en')),
			array('other', array('1.1', 'en')),
		);

        return array_merge($parameters, $rules);
    }

    /**
     * test getRule
     * expected boolean
     * @dataProvider providerGetRule
     */
    public function testGetRule($rule, $parameters)
    {
        $this->assertSame(
            $rule,
            Plural::getRule($parameters[0], $parameters[1])
        );
    }
	
    public function testExceptionsProvider()
    {
        return array(
            array('getRule', array('not-a-number'), '\\Punic\\Exception\\BadArgumentType'),
            array('getRule', array(true), '\\Punic\\Exception\\BadArgumentType'),
		);
	}
	
    /**
     * @dataProvider testExceptionsProvider
     */
    public function testExceptions($method, $parameters, $exception)
    {
        $this->setExpectedException($exception);
        call_user_func_array(array('\Punic\Plural', $method), $parameters);
    }

}
