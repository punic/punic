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
     * @var \Collator|null
     */
    private $collator;

    /**
     * @var bool
     */
    private $iconv;

    /**
     * Initializes the instance.
     *
     * @param string $locale
     * @param bool $caseSensitive
     */
    public function __construct($locale = null, $caseSensitive = false)
    {
        $this->cache = array();
        $this->locale = isset($locale) ? $locale : \Punic\Data::getDefaultLocale();
        $this->caseSensitive = (bool) $caseSensitive;
        $this->iconv = function_exists('iconv');
        
        if (class_exists('\Collator') && extension_loaded('intl')) {
            $this->collator = new \Collator($this->locale);
        } else {
            $this->collator = null;
        }
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
        if (isset($this->collator)) {
            $a = (string) $a;
            $b = (string) $b;
            if ($this->caseSensitive) {
                $result = $this->collator->compare($a, $b);
            } else {
                $array = array($a, $b);
                if ($this->sort($array) === false) {
                    $result = false;
                } else {
                    $ia = array_search($a, $array);
                    if ($ia === 1) {
                        $result = 1;
                    } else {
                        $ib = array_search($b, $array);
                        if ($ib === 1) {
                            $result = -1;
                        } else {
                            $result = 0;
                        }
                    }
                }
            }
        } else {
            $a = $this->normalize($a);
            $b = $this->normalize($b);

            $result = $this->caseSensitive ? strnatcmp($a, $b) : strnatcasecmp($a, $b);
        }

        return $result;
    }

    /**
     * @param array $array
     * @param bool $keepKeys
     *
     * @return array
     */
    public function sort(&$array, $keepKeys = false)
    {
        $me = $this;
        if ($keepKeys) {
            if (isset($this->collator)) {
                $result = $this->collator->asort($array);
            } else {
                $result = uasort(
                    $array,
                    function ($a, $b) use ($me) {
                        return $me->compare($a, $b);
                    }
                );
            }
        } else {
            if (isset($this->collator)) {
                $result = $this->collator->sort($array);
            } else {
                $result = usort(
                    $array,
                    function ($a, $b) use ($me) {
                        return $me->compare($a, $b);
                    }
                );
            }
        }

        return $result;
    }
}
