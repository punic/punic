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
}
