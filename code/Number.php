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
}
