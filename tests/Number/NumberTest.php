<?php

namespace Punic\Test\Number;

use Punic\Number;
use Punic\Test\TestCase;

class NumberTest extends TestCase
{
    /**
     * @return array
     */
    public function provideIsNumeric()
    {
        return array(
            array(true, '1,234.56', 'en'),
            array(false, '1,234.56', 'it'),
        );
    }

    /**
     * @dataProvider provideIsNumeric
     *
     * @param bool $result
     * @param string $value
     * @param string $locale
     */
    public function testIsNumeric($result, $value, $locale)
    {
        $this->assertSame(
            $result,
            Number::isNumeric($value, $locale)
        );
    }

    /**
     * @return array
     */
    public function provideIsInteger()
    {
        return array(
            array(true, '1,234', 'en'),
            array(false, '1,234', 'it'),
            array(false, '1,234.56', 'en'),
            array(true, '1,234.00', 'en'),
        );
    }

    /**
     * @dataProvider provideIsInteger
     *
     * @param bool $result
     * @param string $value
     * @param string $locale
     */
    public function testIsInteger($result, $value, $locale)
    {
        $this->assertSame(
            $result,
            Number::isInteger($value, $locale)
        );
    }

    /**
     * @return array
     */
    public function provideFormat()
    {
        return array(
            array('1,234.567', 1234.567, null, 'en'),
            array('1,235', 1234.567, 0, 'en'),
            array('1,200', 1234.567, -2, 'en'),
            array('1,234.57', 1234.567, 2, 'en'),
            array('1.234,57', 1234.567, 2, 'it'),
            array('-1,234.57', -1234.567, 2, 'en'),
            array('-1,234.57', '-1234.567', 2, 'en'),
            array('1,234.57', '1234.567', 2, 'en'),
            array('1,234.00', '1234', 2, 'en'),
            array('NaN', NAN, null, 'en'),
            array('∞', INF, null, 'en'),
            array('-∞', -INF, null, 'en'),
            array('', '', null, 'en'),
            array('', false, null, 'en'),
            array('', null, null, 'en'),
            array('', array(), null, 'en'),
            array('', true, null, 'en'),
            array('', '', null, 'en'),
            array('0', '0', null, 'en'),
            array('0', '0.', null, 'en'),
            array('0.0', '.0', null, 'en'),
            array('0.0', '0.0', null, 'en'),
            array('', '.', null, 'en'),
        );
    }

    /**
     * @dataProvider provideFormat
     *
     * @param string $result
     * @param string|mixed $value
     * @param int|null $precision
     * @param string $locale
     */
    public function testFormat($result, $value, $precision, $locale)
    {
        $this->assertSame(
            $result,
            Number::format($value, $precision, $locale)
        );
    }

    /**
     * @return array
     */
    public function provideFormatPercent()
    {
        $nbsp = "\xC2\xA0";

        return array(
            array('12.3456%', 0.123456, null, 'en'),
            array('12%', 0.123456, 0, 'en'),
            array('10%', 0.12, -1, 'en'),
            array('1,234.57%', 12.34567, 2, 'en'),
            array("1.234,57{$nbsp}%", 12.34567, 2, 'da'),
            array('1,234.57‎%‎', 12.34567, 2, 'ar'),
            array('-1,234.57%', -12.34567, 2, 'en'),
            array('-1,234.57%', '-12.34567', 2, 'en'),
            array('1,234.57%', '12.34567', 2, 'en'),
            array('1,230.00%', '12.3', 2, 'en'),
            array('', '', null, 'en'),
            array('', false, null, 'en'),
            array('', null, null, 'en'),
            array('', array(), null, 'en'),
            array('', true, null, 'en'),
            array('', '', null, 'en'),
            array('0%', '0', null, 'en'),
            array('0%', '0.', null, 'en'),
            array('0.0%', '.0', null, 'en'),
            array('0.0%', '0.0', null, 'en'),
            array('', '.', null, 'en'),
        );
    }

    /**
     * @dataProvider provideFormatPercent
     *
     * @param string $result
     * @param string|mixed $value
     * @param int|null $precision
     * @param string $locale
     */
    public function testFormatPercent($result, $value, $precision, $locale)
    {
        $this->assertSame(
            $result,
            Number::formatPercent($value, $precision, $locale)
        );
    }

    /**
     * @return array
     */
    public function provideFormatCurrency()
    {
        $nbsp = "\xC2\xA0";

        return array(
            array('$1.23', 1.23, 'USD', 'standard', null, null, 'en'),
            array('US$1.23', 1.23, 'USD', 'standard', null, null, 'en_CA'),
            array("ZAR{$nbsp}1.23", 1.23, 'ZAR', 'standard', null, null, 'en'),
            array("1,23{$nbsp}€", 1.23, 'EUR', 'standard', null, null, 'de'),
            array("1,23{$nbsp}kr.", 1.23, 'DKK', 'standard', null, null, 'da'),
            array("CLF{$nbsp}1.2300", 1.23, 'CLF', 'standard', null, null, 'en'),
            array("ADP{$nbsp}1", 1.23, 'ADP', 'standard', null, null, 'en'),
            array('$1.2', 1.23, 'USD', 'standard', 1, null, 'en'),
            array('$1.230', 1.23, 'USD', 'standard', 3, null, 'en'),
            array('$100', 123, 'USD', 'standard', -2, null, 'en'),
            array("USD{$nbsp}1.23", 1.23, 'USD', 'standard', null, 'code', 'en'),
            array("TL{$nbsp}1.23", 1.23, 'TRY', 'standard', null, 'alt', 'en'),
            array('1 US dollar', 1, 'USD', 'standard', 0, 'long', 'en'),
            array('1 US dollar', 1.23, 'USD', 'standard', 0, 'long', 'en'),
            array('1.00 US dollars', 1, 'USD', 'standard', null, 'long', 'en'),
            array('1.23 US dollars', 1.23, 'USD', 'standard', null, 'long', 'en'),
            array('1 dolar amerykański', 1, 'USD', 'standard', 0, 'long', 'pl'),
            array('2 dolary amerykańskie', 2, 'USD', 'standard', 0, 'long', 'pl'),
            array('1,00 dolara amerykańskiego', 1, 'USD', 'standard', 2, 'long', 'pl'),
            array('1.23米ドル', 1.23, 'USD', 'standard', null, 'long', 'ja'),
            array("ADP{$nbsp}1", 1.23, 'ADP', 'standard', null, null, 'en'),
            array("UNKNOWN{$nbsp}1.23", 1.23, 'UNKNOWN', 'standard', null, null, 'en'),
            array('-$1.23', -1.23, 'USD', 'standard', null, null, 'en'),
            array('($1.23)', -1.23, 'USD', 'accounting', null, null, 'en'),
            array('', '', 'EUR', 'standard', null, null, 'en'),
            array('', false, 'EUR', 'standard', null, null, 'en'),
            array('', null, 'EUR', 'standard', null, null, 'en'),
            array('', array(), 'EUR', 'standard', null, null, 'en'),
            array('', true, 'EUR', 'standard', null, null, 'en'),
            array('', '', 'EUR', 'standard', null, null, 'en'),
            array('€0', '0', 'EUR', 'standard', 0, null, 'en'),
            array('€0.00', '0.0', 'EUR', 'standard', null, null, 'en'),
            array('', '.', 'EUR', 'standard', null, null, 'en'),
        );
    }

    /**
     * @dataProvider provideFormatCurrency
     *
     * @param string $result
     * @param string|mixed $value
     * @param int|null $precision
     * @param string $locale
     * @param mixed $currencyCode
     * @param mixed $kind
     * @param mixed $symbol
     */
    public function testFormatCurrency($result, $value, $currencyCode, $kind, $precision, $symbol, $locale)
    {
        $this->assertSame(
            $result,
            Number::formatCurrency($value, $currencyCode, $kind, $precision, $symbol, $locale)
        );
    }

    /**
     * @return array
     */
    public function provideUnformat()
    {
        return array(
            array(1234.567, '1,234.567', 'en'),
            array(1235, '1,235', 'en'),
            array((float) 1235, '1,235.', 'en'),
            array((float) 1235, '1,235.0', 'en'),
            array(1234.57, '1.234,57', 'it'),
            array(-1234.57, '-1,234.57', 'en'),
            array(1234, 1234, 'en'),
        );
    }

    /**
     * @dataProvider provideUnformat
     *
     * @param float|int $result
     * @param string|number $value
     * @param string $locale
     */
    public function testUnformat($result, $value, $locale)
    {
        $this->assertSame(
            $result,
            Number::unformat($value, $locale)
        );
    }

    public function provideExceptions()
    {
        return array(
            array('formatCurrency', array(0, 'EUR', 'invalid'), 'Punic\\Exception'),
        );
    }

    /**
     * @dataProvider provideExceptions
     *
     * @param string $method
     * @param array $parameters
     * @param string $exception
     */
    public function testExceptions($method, $parameters, $exception)
    {
        $this->setExpectedException($exception);
        call_user_func_array(array('Punic\Number', $method), $parameters);
    }

    /**
     * @return array
     */
    public function provideSpellOut()
    {
        return array(
            array('', 'foo', 'spellout-numbering', 'en'),
            array('', false, 'spellout-numbering', 'en'),
            array('', null, 'spellout-numbering', 'en'),
            array('zero', 0, 'spellout-numbering', 'en'),
            array('one', 1, 'spellout-numbering', 'en'),
            array('minus one', -1, 'spellout-numbering', 'en'),
            array('twelve', 12, 'spellout-numbering', 'en'),
            array('one thousand two hundred thirty-four', 1234, 'spellout-numbering', 'en'),
            array('ten billion one', 10000000001, 'spellout-numbering', 'en'),
            array('ten billion and one', 10000000001, 'spellout-numbering-verbose', 'en'),
            array('zero point one two', 0.12, 'spellout-numbering', 'en'),
            array('one point two three', 1.23, 'spellout-numbering', 'en'),
            array('infinity', INF, 'spellout-numbering', 'en'),
            array('minus infinity', -INF, 'spellout-numbering', 'en'),
            array('not a number', NAN, 'spellout-numbering', 'en'),
            array('영점이', 0.2, 'spellout-numbering', 'ko'),
            array('일점이', 1.2, 'spellout-numbering', 'ko'),
            array('one thousand nine hundred eighty-four', 1984, 'spellout-numbering', 'en'),
            array('nineteen eighty-four', 1984, 'spellout-numbering-year', 'en'),
            array('two thousand one', 2001, 'spellout-numbering-year', 'en'),
            array('一九八四', 1984, 'spellout-numbering-year', 'ja'),
            array('二〇〇一', 2001, 'spellout-numbering-year', 'ja'),
            array('first', 1, 'spellout-ordinal', 'en'),
            array('seventeenth', 17, 'spellout-ordinal', 'en'),
            array('one hundredth', 100, 'spellout-ordinal', 'en'),
            array('one hundred twenty-third', 123, 'spellout-ordinal', 'en'),
            array('one hundred and twenty-third', 123, 'spellout-ordinal-verbose', 'en'),
            array('123.45', 123.45, 'spellout-ordinal', 'en'),
            array('0th', 0, 'digits-ordinal', 'en'),
            array('1st', 1, 'digits-ordinal', 'en'),
            array('2nd', 2, 'digits-ordinal', 'en'),
            array('3rd', 3, 'digits-ordinal', 'en'),
            array('4th', 4, 'digits-ordinal', 'en'),
            array('21st', 21, 'digits-ordinal', 'en'),
            array('1.', 1, 'digits-ordinal', 'de'),
            array('1:a', 1, 'digits-ordinal', 'sv'),
            array('I', 1, 'roman-upper', null),
            array('mcmlxxxiv', 1984, 'roman-lower', null),
            array('MCMLXXXIV', 1984, 'roman-upper', 'en'),
            array('1,000,000', 1000000, 'roman-upper', 'en'),
            array('1.000.000', 1000000, 'roman-upper', 'nl'),
            array('N', 0, 'roman-upper', 'en'),
            array('−I', -1, 'roman-upper', 'en'),
            array('0.20', .2, 'tamil', null),
            array('௨௲௧', 2001, 'tamil', null),
            array('0.20', .2, 'hebrew', null),
            array('ב׳א׳', 2001, 'hebrew', null),
            array('תתתתתא', 2001, 'hebrew-item', null),
            array('תתתתתא', 2001, 'hebrew-item', null),
            array('𐆊´.Β´', .2, 'greek-upper', null),
            array('͵ΒΑ´', 2001, 'greek-upper', null),
            array('ባዶ፡፪', .2, 'ethiopic', null),
            array('፳፻፩', 2001, 'ethiopic', null),
        );
    }

    /**
     * @dataProvider provideSpellOut
     *
     * @param string $result
     * @param string|number $value
     * @param string $locale
     * @param mixed $type
     */
    public function testSpellOut($result, $value, $type, $locale)
    {
        $this->assertSame(
            $result,
            Number::spellOut($value, $type, $locale)
        );
    }

    public function testSpellOutException()
    {
        $this->setExpectedException('Punic\Exception\ValueNotInList');
        Number::spellOut(1234, 'foo', 'en');
    }
}
