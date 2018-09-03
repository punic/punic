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

    /**
     * @return array
     */
    public function providerSpellOut()
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
     * @dataProvider providerSpellOut
     *
     * @param strinf $result
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
