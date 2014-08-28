<?php
namespace Punic;

/**
 * Numbers helpers
 */
class Number
{
    /**
     * Check if a variable contains a valid number for the specified locale
     * @param string $value The string value to check
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return bool
     */
    public static function isNumeric($value, $locale = '')
    {
        $result = false;
        if (is_int($value) || is_float($value)) {
            $result = true;
        } elseif (is_string($value) && strlen($value)) {
            $data = \Punic\Data::get('numbers', $locale);
            if (strpos($value, $data['symbols']['decimal']) === 0) {
                $value = '0' . $value;
            }
            foreach (array('decimalFormats', 'scientificFormats') as $kind) {
                foreach (array('rx+', 'rx-') as $sign) {
                    if (preg_match($data[$kind]['standard'][$sign], $value)) {
                        $result = true;
                        break;
                    }
                }
                if ($result === true) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Convert a localized representation of a number to a number (for instance, converts the string '1,234' to 1234 in case of English and to 1.234 in case of Italian)
     * @param string $value The string value to convert
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return int|float|null Returns null if $value is not valid, the numeric value otherwise
     */
    public static function unformat($value, $locale = '')
    {
        $result = null;
        if (is_int($value) || is_float($value)) {
            $result = $value;
        } elseif (is_string($value) && static::isNumeric($value, $locale)) {
            $data = \Punic\Data::get('numbers', $locale);
            $decimal = $data['symbols']['decimal'];
            $minus = $data['symbols']['minusSign'];
            $simplified = strtoupper(preg_replace('/[^0-9eE'. preg_quote($decimal) . preg_quote($minus) . ']/', '', $value));
            if ((strpos($simplified, $decimal) === false) && (strpos($simplified, 'E') === false)) {
                $result = intval($simplified);
            } else {
                $result = floatval(str_replace($decimal, '.', $simplified));
            }
        }

        return $result;
    }
}
