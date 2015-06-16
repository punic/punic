<?php

namespace Punic;

/**
 * Various helper stuff.
 */
class Comparer
{
    /**
     * @var array
     */
    private $cache;

    /**
     * @var bool
     */
    private $caseSensitive;

    /**
     * @var bool
     */
    private $iconv;

    /**
     * Initializes the instance.
     *
     * @param bool $caseSensitive
     */
    public function __construct($caseSensitive = false)
    {
        $this->cache = array();
        $this->caseSensitive = (bool) $caseSensitive;
        $this->iconv = function_exists('iconv');
    }

    /**
     * @param string $str
     *
     * @return string
     */
    private function normalize($str)
    {
        $str = (string) $str;
        if (!isset($this->cache[$str])) {
            $this->cache[$str] = $str;
            if ($str !== '') {
                if ($this->iconv) {
                    $transliterated = @iconv('UTF-8', 'ASCII//IGNORE//TRANSLIT', $str);
                    if ($transliterated !== false) {
                        $this->cache[$str] = $transliterated;
                    }
                }
            }
        }

        return $this->cache[$str];
    }

    /**
     * Compare two strings.
     *
     * @param string $a
     * @param string $b
     *
     * @return int
     */
    public function compare($a, $b)
    {
        $a = $this->normalize($a);
        $b = $this->normalize($b);

        return $this->caseSensitive ? strnatcmp($a, $b) : strnatcasecmp($a, $b);
    }

    /**
     * @param array $array
     * @param bool $keepKeys
     *
     * @return array
     */
    public function sort($array, $keepKeys = false)
    {
        $me = $this;
        if ($keepKeys) {
            uasort(
                $array,
                function ($a, $b) use ($me) {
                    return $me->compare($a, $b);
                }
            );
        } else {
            usort(
                $array,
                function ($a, $b) use ($me) {
                    return $me->compare($a, $b);
                }
            );
        }

        return $array;
    }
}
