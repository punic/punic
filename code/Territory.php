<?php
namespace Punic;

/**
 * Territory-related stuff
 */
class Territory
{

    /**
     * Retrieve the name of a territory (country, continent, ...)
     * @param string $territoryCode The territory code
     * @param string $locale = '' The locale to use. If empty we'll use the default locale set in \Punic\Data
     * @return string Returns the localized territory name (returns $territoryCode if not found)
     */
    public static function getName($territoryCode, $locale = '')
    {
        $result = $territoryCode;
        if (preg_match('/^[a-z0-9]{2,3}$/i', $territoryCode)) {
            $territoryCode = strtoupper($territoryCode);
            $data = Data::get('territories', $locale);
            if (array_key_exists($territoryCode, $data)) {
                $result = $data[$territoryCode];
            }
        }

        return $result;
    }
}
