<?php
namespace Punic;

class Data
{
    /**
     * Let's cache already loaded files
     * @var array
     */
    protected static $cache = array();

    /**
     * The current default locale
     * @var string
     */
    protected static $defaultLocale = 'en_US';

    /**
     * The current default language
     * @var string
     */
    protected static $defaultLanguage = 'en';

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
        return static::$defaultLanguage;
    }

    /**
     * Set the current default locale and language
     * @param string $locale
     * @throws \Exception Throws an exception if $locale is not a valid string
     */
    public static function setDefaultLocale($locale)
    {
        if (!(is_string($locale) && strlen($locale))) {
           throw new \Exception('Invalid parameter');
        }
        static::$defaultLocale = $locale;
        static::$defaultLanguage = array_shift(explode('_', str_replace('-', '_', $locale), 2));
    }

    /**
     * Get the locale data
     * @param string $identifier The data identifier
     * @param string $locale The locale identifier (if empty we'll use the current default locale)
     * @return array
     * @throws \Exception Throws an exception in case of problems
     */
    public static function get($identifier, $locale = '')
    {
        if (empty($locale)) {
            $locale = static::$defaultLocale;
            $language = static::$defaultLanguage;
        } else {
            $language = array_shift(explode('_', str_replace('-', '_', $locale), 2));
        }
        if (!array_key_exists($locale, static::$cache)) {
            static::$cache[$locale] = array();
        }
        if (!array_key_exists($identifier, static::$cache[$locale])) {
            $file = null;
            if (preg_match('/^[a-z0-1_]+$/i', $locale) && preg_match('/^[a-z0-1_]+$/i', $identifier)) {
                $file = "/data/$locale/$identifier.json";
                if (!is_file(__DIR__ . $file)) {
                    $file = "/data/$language/$identifier.json";
                    if (!is_file(__DIR__ . $file)) {
                        $file = null;
                    }
                }
            }
            if (is_null($file)) {
                throw new \Exception("Invalid data '$identifier' for locale '$locale'");
            }
            $data = @file_get_contents(__DIR__ . $file);
            if ($data === false) {
                throw new \Exception("Unable to read from file $file");
            }
            $data = @json_decode($data, true);
            if (!is_array($data)) {
                throw new \Exception("Bad data read from file $file");
            }
            static::$cache[$locale][$identifier] = $data;
        }

        return static::$cache[$locale][$identifier];
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
                        $locales[] = $item;
                    }
                }
            }
        }

        return $locales;
    }

}
