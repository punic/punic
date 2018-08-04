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
        if (is_numeric($value)) {
            if (is_string($value) && $precision === null) {
                $precision = self::getPrecision($value);
            }
            $number = (float) $value;
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
     * Localize a percentage (for instance, converts 12.345 to '1,234.5%' in case of English and to '1.234,5 %' in case of Danish).
     *
     * @param int|float|string $value The string value to convert
     * @param int|null $precision The wanted precision (well use {@link http://php.net/manual/function.round.php})
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return string Returns an empty string $value is not a number, otherwise returns the localized representation of the percentage
     */
    public static function formatPercent($value, $precision = null, $locale = '')
    {
        $result = '';
        if (is_numeric($value)) {
            $data = Data::get('numbers', $locale);

            if ($precision === null) {
                $precision = self::getPrecision($value);
            }
            $formatted = self::format(100 * abs($value), $precision, $locale);

            $format = $data['percentFormats']['standard'][$value >= 0 ? 'positive' : 'negative'];
            $sign = $data['symbols']['percentSign'];

            $result = sprintf($format, $formatted, $sign);
        }

        return $result;
    }

    /**
     * Localize a currency amount (for instance, converts 12.345 to '1,234.5%' in case of English and to '1.234,5 %' in case of Danish).
     *
     * @param int|float|string $value The string value to convert
     * @param string $currencyCode the 3-letter currency code
     * @param string $kind the currency variant, either "standard" or "accounting"
     * @param int|null $precision The wanted precision (well use {@link http://php.net/manual/function.round.php})
     * @param string $locale The locale to use. If empty we'll use the default locale set in \Punic\Data
     *
     * @return string Returns an empty string $value is not a number, otherwise returns the localized representation of the percentage
     */
    public static function formatCurrency($value, $currencyCode, $kind = 'standard', $precision = null, $locale = '')
    {
        $result = '';
        if (is_numeric($value)) {
            $data = Data::get('numbers', $locale);
            $data = $data['currencyFormats'];

            if ($precision === null) {
                $currencyData = Data::getGeneric('currencyData');
                if (isset($currencyData['fractions'][$currencyCode]['digits'])) {
                    $precision = $currencyData['fractions'][$currencyCode]['digits'];
                } else {
                    $precision = $currencyData['fractionsDefault']['digits'];
                }
            }
            $formatted = self::format(abs($value), $precision, $locale);

            if (!isset($data[$kind])) {
                throw new Exception\ValueNotInList($kind, array_keys($data));
            }
            $format = $data[$kind][$value >= 0 ? 'positive' : 'negative'];
            $symbol = Currency::getSymbol($currencyCode, null, $locale);
            if (!$symbol) {
                $symbol = $currencyCode;
            }

            list($before, $after) = explode('%2$s', $format);
            if ($after &&
                preg_match($data['currencySpacing']['afterCurrency']['currency'], $symbol) &&
                preg_match($data['currencySpacing']['afterCurrency']['surrounding'], sprintf($after, $formatted))) {
                $symbol .= $data['currencySpacing']['afterCurrency']['insertBetween'];
            }
            if ($before &&
                preg_match($data['currencySpacing']['beforeCurrency']['currency'], $symbol) &&
                preg_match($data['currencySpacing']['beforeCurrency']['surrounding'], sprintf($before, $formatted))) {
                $symbol = $data['currencySpacing']['beforeCurrency']['insertBetween'].$symbol;
            }

            $result = sprintf($format, $formatted, $symbol);
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

    private static function getPrecision($value)
    {
        $precision = null;
        if (is_string($value)) {
            $i = strrpos($value, '.');
            if ($i !== false) {
                $precision = strlen($value) - $i - 1;
            }
        }

        return $precision;
    }
}
