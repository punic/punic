<?php

namespace Punic;

/**
 * Plural helper stuff.
 */
class Plural
{
    /**
     * Return the list of applicable plural rule for a locale.
     *
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return array<string> Returns a list containing some the following values: 'zero', 'one', 'two', 'few', 'many', 'other' ('other' will be always there)
     */
    public static function getRules($locale = '')
    {
        $node = Data::getLanguageNode(Data::getGeneric('plurals'), $locale);

        return array_merge(
            array_keys($node),
            array('other')
        );
    }

    /**
     * Return the plural rule ('zero', 'one', 'two', 'few', 'many' or 'other') for a number and a locale.
     *
     * @param string|int|float $number The number to check the plural rule for for
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @throws \Punic\Exception\BadArgumentType Throws a \Punic\Exception\BadArgumentType if $number is not a valid number
     * @throws \Exception Throws a \Exception if there were problems calculating the plural rule
     *
     * @return string Returns one of the following values: 'zero', 'one', 'two', 'few', 'many', 'other'
     */
    public static function getRule($number, $locale = '')
    {
        if (is_int($number)) {
            $intPartAbs = (string) abs($number);
            $floatPart = '';
        } elseif (is_float($number)) {
            $s = (string) $number;
            if (strpos($s, '.') === false) {
                $intPart = $s;
                $floatPart = '';
            } else {
                list($intPart, $floatPart) = explode('.', $s);
            }
            $intPartAbs = (string) abs((int) $intPart);
        } elseif (is_string($number) && $number !== '') {
            if (preg_match('/^[+|\\-]?\\d+\\.?$/', $number)) {
                $v = (int) $number;
                $intPartAbs = (string) abs($v);
                $floatPart = '';
            } elseif (preg_match('/^(\\d*)\\.(\\d+)$/', $number, $m)) {
                list($intPart, $floatPart) = explode('.', $number);
                $v = @(int) $intPart;
                $intPartAbs = (string) abs($v);
            } else {
                throw new Exception\BadArgumentType($number, 'number');
            }
        } else {
            throw new Exception\BadArgumentType($number, 'number');
        }
        // 'n' => '%1$s', // absolute value of the source number (integer and decimals).
        $v1 = $intPartAbs.(strlen($floatPart) ? ".$floatPart" : '');
        // 'i' => '%2$s', // integer digits of n
        $v2 = $intPartAbs;
        // 'v' => '%3$s', // number of visible fraction digits in n, with trailing zeros.
        $v3 = strlen($floatPart);
        // 'w' => '%4$s', // number of visible fraction digits in n, without trailing zeros.
        $v4 = strlen(rtrim($floatPart, '0'));
        // 'f' => '%5$s', // visible fractional digits in n, with trailing zeros.
        $v5 = strlen($floatPart) ? (string) ((int) $floatPart) : '0';
        // 't' => '%6$s', // visible fractional digits in n, without trailing zeros.
        $v6 = trim($floatPart, '0');
        if ($v6 === '') {
            $v6 = '0';
        }
        $result = 'other';
        $node = Data::getLanguageNode(Data::getGeneric('plurals'), $locale);
        foreach ($node as $rule => $formulaPattern) {
            $formula = sprintf($formulaPattern, $v1, $v2, $v3, $v4, $v5, $v6);
            $check = str_replace(array('static::inRange(', ' and ', ' or ', ', false, ', ', true, ', ', array('), ' , ', $formula);
            if (preg_match('/[a-z]/', $check)) {
                throw new \Exception('Bad formula!');
            }
            // fix for difference in modulo (%) in the definition and the one implemented in PHP for decimal numbers
            while (preg_match('/(\\d+\\.\\d+) % (\\d+(\\.\\d+)?)/', $formula, $m)) {
                list(, $decimalPart) = explode('.', $m[1], 2);
                $decimals = strlen(rtrim($decimalPart, '0'));
                if ($decimals > 0) {
                    $pow = (int) pow(10, $decimals);
                    $repl = '('.(string) ((int) ((float) $m[1] * $pow)).' % '.(string) ((int) ((float) ($m[2] * $pow))).') / '.$pow;
                } else {
                    $repl = (string) ((int) $m[1]).' % '.$m[2];
                }
                $formula = str_replace($m[0], $repl, $formula);
            }
            $formulaResult = @eval("return ($formula) ? 'yes' : 'no';");
            if ($formulaResult === 'yes') {
                $result = $rule;
                break;
            } elseif ($formulaResult !== 'no') {
                throw new \Exception('There was a problem in the formula '.$formulaPattern);
            }
        }

        return $result;
    }

    /**
     * @param int|string|array $value
     * @param bool $mustBeIncluded
     *
     * @return bool
     */
    protected static function inRange($value, $mustBeIncluded)
    {
        if (is_int($value)) {
            $isInt = true;
        } elseif ((int) $value == $value) {
            $isInt = true;
        } else {
            $isInt = false;
        }
        $rangeValues = (func_num_args() > 2) ? array_slice(func_get_args(), 2) : array();
        $included = false;
        foreach ($rangeValues as $rangeValue) {
            if (is_array($rangeValue)) {
                if ($isInt && ($value >= $rangeValue[0]) && ($value <= $rangeValue[1])) {
                    $included = true;
                    break;
                }
            } elseif ($value == $rangeValue) {
                $included = true;
                break;
            }
        }

        return $included == $mustBeIncluded;
    }
}
