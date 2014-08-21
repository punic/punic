<?php
namespace Punic;

class Data
{
    /**
     * Let's cache already loaded files (locale-specific)
     * @var array
     */
    protected static $cache = array();

    /**
     * Let's cache already loaded files (not locale-specific)
     * @var array
     */
    protected static $cacheGeneric = array();

    /**
     * The current default locale
     * @var string
     */
    protected static $defaultLocale = 'en_US';

    /**
     * The fallback locale (used if default locale is not found)
     * @var string
     */
    protected static $fallbackLocale = 'en_US';

    /**
     * Return the current default locale
     * @return string
     */
    public static function getDefaultLocale()
    {
        return static::$defaultLocale;
    }

    /**
     * Return the current default language
     * @return string
     */
    public static function getDefaultLanguage()
    {
        $info = static::explodeLocale(static::$defaultLanguage);

        return $info['language'];
    }

    /**
     * Set the current default locale and language
     * @param string $locale
     * @throws \Exception Throws an exception if $locale is not a valid string
     */
    public static function setDefaultLocale($locale)
    {
        if (is_null(static::explodeLocale($locale))) {
           throw new \Exception("'$locale' is not a valid locale identifier");
        }
        static::$defaultLocale = $locale;
    }

    /**
     * Return the current fallback locale (used if default locale is not found)
     * @return string
     */
    public static function getFallbackLocale()
    {
        return static::$fallbackLocale;
    }

    /**
     * Return the current fallback language (used if default locale is not found)
     * @return string
     */
    public static function getFallbackLanguage()
    {
        $info = static::explodeLocale(static::$fallbackLanguage);

        return $info['language'];
    }

    /**
     * Set the current fallback locale and language
     * @param string $locale
     * @throws \Exception Throws an exception if $locale is not a valid string
     */
    public static function setFallbackLocale($locale)
    {
        if (is_null(static::explodeLocale($locale))) {
            throw new \Exception("'$locale' is not a valid locale identifier");
        }
        if (static::$fallbackLocale !== $locale) {
            static::$fallbackLocale = $locale;
            static::$cache = array();
        }
    }

    /**
     * Get the locale data
     * @param string $identifier The data identifier
     * @param string $locale ='' The locale identifier (if empty we'll use the current default locale)
     * @return array
     * @throws \Exception Throws an exception in case of problems
     */
    public static function get($identifier, $locale = '')
    {
        if (empty($locale)) {
            $locale = static::$defaultLocale;
        }
        if (!array_key_exists($locale, static::$cache)) {
            static::$cache[$locale] = array();
        }
        if (!@array_key_exists($identifier, static::$cache[$locale])) {
            if (!@preg_match('/^[a-zA-Z0-1_\\-]+$/i', $identifier)) {
                throw new \Exception("Invalid file identifier specification: '$identifier'");
            }
            $dir = static::getLocaleFolder($locale);
            if (!strlen($dir)) {
                throw new \Exception("Unable to find the specified locale folder (neither '$locale' nor '" . static::$fallbackLocale . "' folders were found");
            }
            $file = $dir . DIRECTORY_SEPARATOR . $identifier . '.json';
            if (!is_file($file)) {
                throw new \Exception("Invalid data '$identifier' for locale '$locale'");
            }
            $data = @file_get_contents($file);
            if ($data === false) {
                throw new \Exception("Unable to read from file '$identifier' for locale '$locale'");
            }
            $data = @json_decode($data, true);
            if (!is_array($data)) {
                throw new \Exception("Bad data read from file '$identifier' for locale '$locale'");
            }
            static::$cache[$locale][$identifier] = $data;
        }

        return static::$cache[$locale][$identifier];
    }

    /**
     * Get the generic data
     * @param string $identifier The data identifier
     * @return array
     * @throws \Exception Throws an exception in case of problems
     */
    public static function getGeneric($identifier)
    {
        if (is_string($identifier) && strlen($identifier)) {
            if (array_key_exists($identifier, static::$cacheGeneric)) {
                return static::$cacheGeneric[$identifier];
            }
            if (preg_match('/^[a-zA-Z0-1_\\-]+$/', $identifier)) {
                $file = "/data/$identifier.json";
                if (is_file(__DIR__ . $file)) {
                    $data = @file_get_contents(__DIR__ . $file);
                    if ($data === false) {
                        throw new \Exception("Unable to read from file $file");
                    }
                    $data = @json_decode($data, true);
                    if (!is_array($data)) {
                        throw new \Exception("Bad data read from file $file");
                    }
                    static::$cacheGeneric[$identifier] = $data;

                    return $data;
                }
            }
        }
        throw new \Exception("Invalid data '$identifier'");
    }

    /**
     * Return a list of available locale identifiers
     * @return array
     */
    public static function getAvailableLocales()
    {
        $locales = array();
        $dir = __DIR__ . '/data';
        if (is_dir($dir) && is_readable($dir)) {
            $contents = @scandir($dir);
            if (is_array($contents)) {
                foreach (array_diff($contents, array('.', '..')) as $item) {
                    if (is_dir($dir . '/' . $item)) {
                        if ($item === 'root') {
                            $item = 'en-US';
                        }
                        $locales[] = $item;
                    }
                }
            }
        }

        return $locales;
    }

    /**
     * Try to guess the full locale (with script and territory) ID associated to a language
     * @param string $language ='' The language identifier (if empty we'll use the current default language)
     * @param string $script ='' The script identifier (if $language is empty we'll use the current default script)
     * @return string Returns an empty string if the territory was not found, the territory ID otherwise
     */
    public static function guessFullLocale($language = '', $script = '')
    {
        $result = '';
        if (empty($language)) {
            $defaultInfo = static::explodeLocale($locale);
            $language = $defaultInfo['language'];
            $script = $defaultInfo['script'];
        }
        $data = static::getGeneric('likelySubtags');
        $keys = array();
        if (!empty($script)) {
            $keys[] = "$language-$script";
        }
        $keys[] = $language;
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                $result = $data[$key];
                break;
            }
        }

        return $result;
    }

    /**
     * Return the terrotory associated to the locale (guess it if it's not present in $locale)
     * @param string $locale ='' The locale identifier (if empty we'll use the current default locale)
     * @return string
     */
    public static function getTerritory($locale = '', $checkFallbackLocale = true)
    {
        $result = '';
        if (empty($locale)) {
            $locale = static::$defaultLocale;
        }
        $info = static::explodeLocale($locale);
        if (is_array($info)) {
            if (!strlen($info['territory'])) {
                $fullLocale = static::guessFullLocale($info['language'], $info['script']);
                if (strlen($fullLocale)) {
                    $info = static::explodeLocale($fullLocale);
                }
            }
            if (strlen($info['territory'])) {
                $result = $info['territory'];
            } elseif ($checkFallbackLocale) {
                $result = self::getTerritory(self::$fallbackLocale, false);
            }
        }

        return $result;
    }

    /**
     * Return the parent of a territory
     * @param string $territory The child territory
     * @return string Returns an empty string if the parent territory was not found, the parent territory ID if found
     */
    protected static function getParentTerritory($territory)
    {
        $result = '';
        if (is_string($territory) && strlen($territory)) {
            foreach (static::getGeneric('territoryContainment') as $parent => $info) {
                if (in_array($territory, $info['contains'], true)) {
                    $result = $parent;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Return the node associated to the locale territory
     * @param string $locale ='' The locale identifier (if empty we'll use the current default locale)
     * @return mixed Returns null if the node was not found, the node data otherwise
     */
    public static function getTerritoryNode($data, $locale = '')
    {
        $result = null;
        $territory = static::getTerritory($locale);
        while (strlen($territory)) {
            if (array_key_exists($territory, $data)) {
                $result = $data[$territory];
                break;
            }
            $territory = static::getParentTerritory($territory);
        }

        return $result;
    }

    /**
     * Returns the item of an array associated to a locale
     * @param array $data The data containing the locale info
     * @param string $locale ='' The locale identifier (if empty we'll use the current default locale)
     * @return mixed Returns null if $data is not an array or it does not contain locale info, the array item otherwise
     */
    public static function getLocaleItem($data, $locale = '')
    {
        $result = null;
        if (is_array($data)) {
            if (empty($locale)) {
                $locale = static::$defaultLocale;
            }
            foreach (static::getLocaleAlternatives($locale) as $alternative) {
                if (array_key_exists($alternative, $data)) {
                    $result = $data[$alternative];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Parse a string representing a locale and extract its components.
     * @param string $locale
     * @return Return null if $locale is not valid; if $locale is valid returns an array with keys 'language', 'script', 'territory'
     */
    protected static function explodeLocale($locale)
    {
        $result = null;
        if (is_string($locale)) {
            if ($locale === 'root') {
                $locale = 'en-US';
            }
            $chunks = explode('-', str_replace('_', '-', strtolower($locale)));
            if (count($chunks) <= 3) {
                if (preg_match('/^[a-z]{2,3}$/', $chunks[0])) {
                    $language = $chunks[0];
                    $script = '';
                    $territory = '';
                    $parentLocale = '';
                    $ok = true;
                    for ($i = 1; $ok && ($i < count($chunks)); $i++) {
                        if (preg_match('/^[a-z]{4}$/', $chunks[$i])) {
                            if (strlen($script) > 0) {
                                $ok = false;
                            } else {
                                $script = ucfirst($chunks[$i]);
                            }
                        } elseif (preg_match('/^([a-z]{2})|([0-9]{3})$/', $chunks[$i])) {
                            if (strlen($territory) > 0) {
                                $ok = false;
                            } else {
                                $territory = strtoupper($chunks[$i]);
                            }
                        } else {
                            $ok = false;
                        }
                    }
                    if ($ok) {
                        $parentLocales = static::getGeneric('parentLocales');
                        if (strlen($script) && strlen($territory) && array_key_exists("$language-$script-$territory", $parentLocales)) {
                            $parentLocale = $parentLocales["$language-$script-$territory"];
                        } elseif (strlen($script) && array_key_exists("$language-$script", $parentLocales)) {
                            $parentLocale = $parentLocales["$language-$script"];
                        } elseif (strlen($territory) && array_key_exists("$language-$territory", $parentLocales)) {
                            $parentLocale = $parentLocales["$language-$territory"];
                        } elseif (array_key_exists($language, $parentLocales)) {
                            $parentLocale = $parentLocales[$language];
                        }
                        $result = array(
                            'language' => $language,
                            'script' => $script,
                            'territory' => $territory,
                            'parentLocale' => $parentLocale
                        );
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Returns the path of the locale-specific data, looking also for the fallback locale
     * @param string $locale The locale for which you want the data folder
     * @return string Returns an empty string if the folder is not found, the absolute path to the folder otherwise
     */
    protected static function getLocaleFolder($locale)
    {
        static $cache = array();
        $result = '';
        if (is_string($locale)) {
            $key = $locale . '/' . static::$fallbackLocale;
            if (!array_key_exists($key, $cache)) {
                foreach (static::getLocaleAlternatives($locale) as $alternative) {
                    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . $alternative;
                    if (is_dir($dir)) {
                        $result = $dir;
                        break;
                    }
                }
                $cache[$key] = $result;
            }
            $result = $cache[$key];
        }

        return $result;
    }

    /**
     * Returns a list of locale identifiers associated to a locale
     * @param string $locale The locale for which you want the alternatives
     * @param string $addFallback = true Set to true to add the fallback locale to the result, false otherwise
     * @return array
     */
    protected static function getLocaleAlternatives($locale, $addFallback = true)
    {
        $result = array();
        $localeInfo = static::explodeLocale($locale);
        if (is_array($localeInfo)) {
            extract($localeInfo);
            if (!strlen($territory)) {
                $fullLocale = static::guessFullLocale($language, $script);
                if (strlen($fullLocale)) {
                    extract(static::explodeLocale($fullLocale));
                }
            }
            if (strlen($script) && strlen($territory)) {
                $result[] = "{$language}-{$script}-{$territory}";
            }
            if (strlen($script)) {
                $result[] = "{$language}-{$script}";
            }
            if (strlen($territory)) {
                $result[] = "{$language}-{$territory}";
                if ("{$language}-{$territory}" === 'en-US') {
                    $result[] = 'root';
                }
            }
            if (strlen($parentLocale)) {
                $result = array_merge($result, static::getLocaleAlternatives($parentLocale, false));
            }
            $result[] = $language;
            if ($addFallback && ($locale !== static::$fallbackLocale)) {
                $result = array_merge($result, static::getLocaleAlternatives(static::$fallbackLocale, false));
            }
            for ($i = count($result) - 1; $i > 1; $i--) {
                for ($j = 0; $j < $i; $j++) {
                    if ($result[$i] === $result[$j]) {
                        array_splice($result, $i, 1);
                        break;
                    }
                }
            }
            $i = array_search('root', $result, true);
            if ($i !== false) {
                array_splice($result, $i, 1);
                $result[] = 'root';
            }
        }

        return $result;
    }
}
