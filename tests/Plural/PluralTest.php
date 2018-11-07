<?php

use Punic\Plural;

class PluralTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function providerGetRules()
    {
        $data = static::loadPluralRulesTestData();
        $parameters = array();
        foreach ($data as $language => $languageTest) {
            $rules = array_keys($languageTest);
            $parameters[] = array(
                static::joinPluralRules($rules),
                $language,
            );
        }

        return $parameters;
    }

    /**
     * test getRules
     * expected boolean.
     *
     * @dataProvider providerGetRules
     *
     * @param string $rules
     * @param string $language
     */
    public function testGetRules($rules, $language)
    {
        $this->assertSame(
            $rules,
            static::joinPluralRules(\Punic\Plural::getRules($language))
        );
    }

    /**
     * @return array
     */
    public function providerGetRuleOfType()
    {
        $data = static::loadPluralRulesTestData();
        $parameters = array();
        foreach ($data as $language => $rules) {
            foreach ($rules as $rule => $values) {
                foreach ($values as $value) {
                    $parameters[] = array(
                        $rule,
                        array($value, $language),
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
            array('other', array(11, 'en', 'ordinal')),
            array('other', array(12, 'en', 'ordinal')),
            array('other', array(13, 'en', 'ordinal')),
            array('one', array(21, 'en', 'ordinal')),
            array('two', array(22, 'en', 'ordinal')),
            array('few', array(23, 'en', 'ordinal')),
            array('other', array(24, 'en', 'ordinal')),
            array('other', array(21, 'en', 'cardinal')),
        );

        return array_merge($parameters, $rules);
    }

    /**
     * test getRuleOfType
     * expected boolean.
     *
     * @dataProvider providerGetRuleOfType
     *
     * @param string $rule
     * @param array $parameters
     */
    public function testGetRuleOfType($rule, $parameters)
    {
        $this->assertSame(
            $rule,
            Plural::getRuleOfType($parameters[0], isset($parameters[2]) ? $parameters[2] : Plural::RULETYPE_CARDINAL, $parameters[1])
        );
    }

    /**
     * @return array
     */
    public function testExceptionsProvider()
    {
        return array(
            array('getRuleOfType', array('not-a-number', Plural::RULETYPE_CARDINAL), '\\Punic\\Exception\\BadArgumentType'),
            array('getRuleOfType', array(true, Plural::RULETYPE_CARDINAL), '\\Punic\\Exception\\BadArgumentType'),
            array('getRuleOfType', array(0, 'invalid rule type'), '\\Punic\\Exception\\ValueNotInList'),
        );
    }

    /**
     * @dataProvider testExceptionsProvider
     *
     * @param string $method
     * @param array $parameters
     * @param string $exception
     */
    public function testExceptions($method, $parameters, $exception)
    {
        $this->setExpectedException($exception);
        call_user_func_array(array('\Punic\Plural', $method), $parameters);
    }

    /**
     * @param array $rules
     *
     * @return string
     */
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

    /**
     * @throws \Exception
     *
     * @return array
     */
    protected static function loadPluralRulesTestData()
    {
        $testDataFile = dirname(__DIR__).DIRECTORY_SEPARATOR.'dataFiles'.DIRECTORY_SEPARATOR.'plurals.php';
        if (!is_file($testDataFile)) {
            throw new \Exception('Test data file not found: plurals.php');
        }
        $data = @include $testDataFile;
        if (!is_array($data)) {
            throw new \Exception('Test data file not valid: plurals.php');
        }

        return $data;
    }
}
