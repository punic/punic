<?php
namespace Punic;

/**
 * Various helper stuff
 */
class Misc
{
    /**
     * Concatenates a list of items returning a localized string (for instance: array(1, 2, 3) will result in '1, 2, and 3' for English or '1, 2 e 3' for Italian)
     * @param array $list The list to concatenate
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $list is not an array of it it's empty, the joined items otherwise.
     */
    public static function join($list, $locale = '')
    {
        return static::joinInternal($list, null, $locale);
    }

    /**
     * Concatenates a list of unit items returning a localized string (for instance: array('3 ft', '2 in') will result in '3 ft, 2 in'
     * @param array $list The list to concatenate
     * @param string $width = '' The preferred width ('' for default, or 'short' or 'narrow')
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns an empty string if $list is not an array of it it's empty, the joined items otherwise.
     */
    public static function joinUnits($list, $width = '', $locale = '')
    {
        $keys = array();
        if (!empty($width)) {
            switch ($width) {
                case 'narrow':
                    $keys = array('unit-narrow', 'unit-short');
                    break;
                case 'short':
                    $keys = array('unit-short', 'unit-narrow');
                    break;
                default:
                    throw new \Punic\Exception\ValueNotInList($width, array('', 'short', 'narrow'));
            }
        }
        $keys[] = 'unit';

        return static::joinInternal($list, $keys, $locale);
    }

    protected static function joinInternal($list, $keys, $locale)
    {
        $result = '';
        if (is_array($list)) {
            $list = array_values($list);
            $n = count($list);
            switch ($n) {
                case 0:
                    break;
                case 1:
                    $result = strval($list[0]);
                    break;
                default:
                    $allData = \Punic\Data::get('listPatterns', $locale);
                    $data = null;
                    if (!empty($keys)) {
                        foreach ($keys as $key) {
                            if (array_key_exists($key, $allData)) {
                                $data = $allData[$key];
                                break;
                            }
                        }
                    }
                    if (is_null($data)) {
                        $data = $allData['standard'];
                    }
                    if (array_key_exists($n, $data)) {
                        $result = vsprintf($data[$n], $list);
                    } else {
                        $result = sprintf($data['end'], $list[$n - 2], $list[$n - 1]);
                        if ($n > 2) {
                            for ($index = $n - 3; $index > 0; $index --) {
                                $result = sprintf($data['middle'], $list[$index], $result);
                            }
                            $result = sprintf($data['start'], $list[0], $result);
                        }
                    }
                    break;
            }
        }

        return $result;
    }
}
