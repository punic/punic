<?php

use Punic\Number;

class NumberTest extends PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function providerIsNumeric()
    {
        return array(
            array(true, '1,234.56', 'en'),
            array(false, '1,234.56', 'it'),
        );
    }

    /**
     * @dataProvider providerIsNumeric
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
    public function providerIsInteger()
    {
        return array(
            array(true, '1,234', 'en'),
            array(false, '1,234', 'it'),
            array(false, '1,234.56', 'en'),
            array(true, '1,234.00', 'en'),
        );
    }

    /**
     * @dataProvider providerIsInteger
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
    public function providerFormat()
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
     * @dataProvider providerFormat
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
    public function providerFormatPercent()
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
     * @dataProvider providerFormatPercent
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
    public function providerFormatCurrency()
    {
        $nbsp = "\xC2\xA0";

        return array(
            array('$1.23', 1.23, 'USD', 'standard', null, null, 'en'),
            array('US$1.23', 1.23, 'USD', 'standard', null, null, 'en_CA'),
            array("ZAR{$nbsp}1.23", 1.23, 'ZAR', 'standard', null, null, 'en'),
            array("1,23{$nbsp}€", 1.23, 'EUR', 'standard', null, null, 'de'),
            array("1,23{$nbsp}kr.", 1.23, 'DKK', 'standard', null, null, 'da'),
            array("CLF{$nbsp}1.2300", 1.23, 'CLF', 'standard', null, null, 'en'),
            array("AMD{$nbsp}1", 1.23, 'AMD', 'standard', null, null, 'en'),
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
            array("AMD{$nbsp}1", 1.23, 'AMD', 'standard', null, null, 'en'),
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
     * @dataProvider providerFormatCurrency
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
    public function providerUnformat()
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
     * @dataProvider providerUnformat
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

    public function testExceptionsProvider()
    {
        return array(
            array('formatCurrency', array(0, 'EUR', 'invalid'), '\\Punic\\Exception'),
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
        call_user_func_array(array('Punic\Number', $method), $parameters);
    }
}
