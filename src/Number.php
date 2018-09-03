<?php

namespace Punic;

/**
 * Numbers helpers.
 */
class Number
{
    /**
     * Check if a variable contains a valid number for the specified locale.
     *
     * @param string $value The string value to check
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return bool
     */
    public static function isNumeric($value, $locale = '')
    {
        return static::unformat($value, $locale) !== null;
    }

    /**
     * Check if a variable contains a valid integer number for the specified locale.
     *
     * @param string $value The string value to check
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return bool
     */
    public static function isInteger($value, $locale = '')
    {
        $result = false;
        $number = static::unformat($value, $locale);
        if (is_int($number)) {
            $result = true;
        } elseif (is_float($number)) {
            if ($number === (float) round($number)) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Localize a number representation (for instance, converts 1234.5 to '1,234.5' in case of English and to '1.234,5' in case of Italian).
     *
     * @param int|float|string $value The string value to convert
     * @param int|null $precision The wanted precision (well use {@link http://php.net/manual/function.round.php})
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return string Returns an empty string $value is not a number, otherwise returns the localized representation of the number
     */
    public static function format($value, $precision = null, $locale = '')
    {
        $result = '';
        $number = null;
        if (is_int($value) || is_float($value)) {
            $number = $value;
        } elseif (is_string($value) && $value !== '') {
            if (preg_match('/^[\\-+]?\\d+$/', $value)) {
                $number = (int) $value;
            } elseif (preg_match('/^[\\-+]?(\\d*)\\.(\\d*)$/', $value, $m)) {
                if (!isset($m[1])) {
                    $m[1] = '';
                }
                if (!isset($m[2])) {
                    $m[2] = '';
                }
                if ($m[1] !== '' || $m[2] !== '') {
                    $number = (float) $value;
                    if (!is_numeric($precision)) {
                        $precision = strlen($m[2]);
                    }
                }
            }
        }
        if ($number !== null) {
            $precision = is_numeric($precision) ? (int) $precision : null;
            if ($precision !== null) {
                $value = round($value, $precision);
            }
            $data = Data::get('numbers', $locale);
            $decimal = $data['symbols']['decimal'];
            $groupLength = (isset($data['groupLength']) && is_numeric($data['groupLength'])) ? (int) $data['groupLength'] : 3;
            if ($value < 0) {
                $sign = $data['symbols']['minusSign'];
                $value = abs($value);
            } else {
                $sign = '';
            }
            $full = explode('.', (string) $value, 2);
            $intPart = $full[0];
            $floatPath = count($full) > 1 ? $full[1] : '';
            $len = strlen($intPart);
            if (($groupLength > 0) && ($len > $groupLength)) {
                $groupSign = $data['symbols']['group'];
                $preLength = 1 + (($len - 1) % 3);
                $pre = substr($intPart, 0, $preLength);
                $intPart = $pre.$groupSign.implode($groupSign, str_split(substr($intPart, $preLength), $groupLength));
            }
            $result = $sign.$intPart;
            if ($precision === null) {
                if ($floatPath !== '') {
                    $result .= $decimal.$floatPath;
                }
            } elseif ($precision > 0) {
                $result .= $decimal.substr(str_pad($floatPath, $precision, '0', STR_PAD_RIGHT), 0, $precision);
            }
        }

        return $result;
    }

    /**
     * Convert a localized representation of a number to a number (for instance, converts the string '1,234' to 1234 in case of English and to 1.234 in case of Italian).
     *
     * @param string $value The string value to convert
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return int|float|null Returns null if $value is not valid, the numeric value otherwise
     */
    public static function unformat($value, $locale = '')
    {
        $result = null;
        if (is_int($value) || is_float($value)) {
            $result = $value;
        } elseif (is_string($value) && $value !== '') {
            $data = Data::get('numbers', $locale);
            $plus = $data['symbols']['plusSign'];
            $plusQ = preg_quote($plus);
            $minus = $data['symbols']['minusSign'];
            $minusQ = preg_quote($minus);
            $decimal = $data['symbols']['decimal'];
            $decimalQ = preg_quote($decimal);
            $group = $data['symbols']['group'];
            $groupQ = preg_quote($group);
            $ok = true;
            if (preg_match('/^'."($plusQ|$minusQ)?(\\d+(?:$groupQ\\d+)*)".'$/', $value, $m)) {
                $sign = $m[1];
                $int = $m[2];
                $float = null;
            } elseif (preg_match('/^'."($plusQ|$minusQ)?(\\d+(?:$groupQ\\d+)*)$decimalQ".'$/', $value, $m)) {
                $sign = $m[1];
                $int = $m[2];
                $float = '';
            } elseif (preg_match('/^'."($plusQ|$minusQ)?(\\d+(?:$groupQ\\d+)*)$decimalQ(\\d+)".'$/', $value, $m)) {
                $sign = $m[1];
                $int = $m[2];
                $float = $m[3];
            } elseif (preg_match('/^'."($plusQ|$minusQ)?$decimalQ(\\d+)".'$/', $value, $m)) {
                $sign = $m[1];
                $int = '0';
                $float = $m[2];
            } else {
                $ok = false;
                $float = $int = $sign = null;
            }
            if ($ok) {
                if ($sign === $minus) {
                    $sign = '-';
                } else {
                    $sign = '';
                }
                $int = str_replace($group, '', $int);
                if ($float === null) {
                    $result = (int) "$sign$int";
                } else {
                    $result = (float) "$sign$int.$float";
                }
            }
        }

        return $result;
    }

    /**
     * Spell out a number (e.g. "one hundred twenty-three" or "twenty-third") or convert to a different numbering system, e.g Roman numerals.
     *
     * Some types are language-dependent and reflect e.g. gender and case. Refer to the CLDR XML source for supported types.
     *
     * Available numbering systems are specified in the "root" locale.
     *
     * @param int|float|string $value The value to localize/spell out
     * @param string $type The format type, e.g. "spellout-numbering", "spellout-numbering-year", "spellout-cardinal", "digits-ordinal", "roman-upper".
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return string The spelled number
     *
     * @see https://www.unicode.org/repos/cldr/trunk/common/rbnf/
     * @see https://www.unicode.org/repos/cldr/trunk/common/rbnf/root.xml
     */
    public static function spellOut($value, $type, $locale)
    {
        return self::formatRbnf($value, $type, null, $locale);
    }

    protected static function formatRbnf($value, $type, $base, $locale)
    {
        $data = Data::get('rbnf', $locale);
        if (!isset($data[$type])) {
            $data += Data::get('rbnf', 'root');
        }
        if (!isset($data[$type])) {
            throw new Exception\ValueNotInList($type, array_keys($data));
        }
        $data = $data[$type];

        list($rule, $left, $right, $prevBase) = self::getRbnfRule($value, $data, $base);

        $rule = preg_replace_callback('/([<>=])(.*?)\1\1?|\$\((.*?),(.*?)\)\$/', function ($match) use ($value, $left, $right, $type, $prevBase, $locale) {
            if (isset($match[4])) {
                $rule = Plural::getRule($left, $locale, $match[3]);
                if (preg_match('/'.$rule.'{(.*?)}/', $match[4], $match2)) {
                    return $match2[1];
                }
            } else {
                $base = null;
                if ($match[2]) {
                    if ($match[2][0] !== '%') {
                        $i = strpos($match[2], '.');
                        if ($i === false) {
                            $precision = 0;
                        } elseif ($match[2][$i + 1] === '#') {
                            $precision = null;
                        } else {
                            $precision = strspn($match[2], '0', $i + 1);
                        }
                        return self::format($value, $precision, $locale);
                    }
                    $type = substr($match[2], 1);
                }

                switch ($match[1]) {
                    case '=':
                        break;
                    case '<':
                        $value = $left;
                        break;
                    case '>':
                        $value = $right;
                        if ($match[0] == '>>>') {
                            $base = $prevBase;
                        }
                        break;
                }

                return implode(' ', array_map(function($v) use ($type, $base, $locale) {
                    return self::formatRbnf($v, $type, $base, $locale);
                }, (array)$value));
            }
        }, $rule);

        return $rule;
    }

    protected static function getRbnfRule($value, $data, $base = null)
    {
        $left = 0;
        $right = 0;
        $prevBase = 0;
        if (!is_numeric($value)) {
            $rule = '';
        } elseif ($value < 0) {
            $right = -$value;
            $rule = $data['-x']['rule'];
        } elseif (is_infinite($value) && isset($data['Inf'])) {
            $rule = $data['Inf']['rule'];
        } elseif (is_nan($value) && isset($data['NaN'])) {
            $rule = $data['NaN']['rule'];
        } elseif (strpos($value, '.') !== false && isset($data['x.x'])) {
            list($left, $right) = explode('.', $value);
            $right = str_split($right);
            if ($left == 0 && isset($data['0.x'])) {
                $rule = $data['0.x']['rule'];
            } else {
                $rule = $data['x.x']['rule'];
            }
        } else {
            $bases = array_keys($data['integer']);
            if ($base) {
                $i = array_search($base, $bases);
            } else {
                for ($i = count($bases) - 1; $i >= 0; $i--) {
                    $base = $bases[$i];
                    if ($base <= $value) {
                        break;
                    }
                }
            }
            $prevBase = $i > 0 ? $bases[$i - 1] : null;

            $r = $data['integer'][$base] + array('radix' => 10);
            $rule = $r['rule'];
            $radix = $r['radix'];
            $divisor = pow($radix, floor(log($base, $radix)));

            if ($divisor) {
                $right = $value % $divisor;
                $left = floor($value / $divisor);
            } else {
                $left = $value % $radix;
            }

            if ($right) {
                $rule = str_replace(array('[', ']'), '', $rule);
            } else {
                $rule = preg_replace('/\[.*?\]/', '', $rule);
            }
        }

        return array($rule, $left, $right, $prevBase);
    }
}
