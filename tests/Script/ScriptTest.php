<?php

namespace Punic\Test\Unit;

use Punic\Script;
use Punic\Test\TestCase;

class ScriptTest extends TestCase
{
    // At the moment, the script 'Afaka' (code 'Afak') is missing for 'ar'

    /**
     * The code of the missing script.
     *
     * @var string
     */
    const MISSING_SCRIPTCODE = 'Afak';

    /**
     * The code of the locale that's the missing script.
     *
     * @var string
     */
    const MISSING_SCRIPTCODE_FOR = 'ar';

    /**
     * The English name of the missing script.
     *
     * @var string
     */
    const MISSING_SCRIPTCODE_ENGLISHNAME = 'Afaka';

    public function testGetAllScriptCodes()
    {
        $scriptCodes = Script::getAllScriptCodes();
        $this->assertTrue(in_array(static::MISSING_SCRIPTCODE, $scriptCodes, true));
        $this->assertTrue(in_array('Hans', $scriptCodes, true));
        $this->assertTrue(in_array('Hant', $scriptCodes, true));
        $this->assertTrue(in_array('Latn', $scriptCodes, true));
    }

    public function testGetAvailableScriptCodes()
    {
        foreach (array('', 'it', 'it-IT', 'de', 'zh') as $locale) {
            $scriptCodes = Script::getAvailableScriptCodes($locale);
            $this->assertTrue(is_array($scriptCodes));
            $this->assertTrue(in_array('Hans', $scriptCodes, true));
            $this->assertTrue(in_array('Hant', $scriptCodes, true));
            $this->assertTrue(in_array('Latn', $scriptCodes, true));
        }
    }

    /**
     * @return array
     */
    public function getScriptNameProvider()
    {
        return array(
            array('', '', true, 'it', ''),
            array($this, '', true, 'it', ''),
            array(123, '', true, 'it', ''),
            array(array(123), '', true, 'it', ''),
            array('Hant', '', true, 'it', 'tradizionale'),
            array('Hant', 'this is not valid', true, 'it', 'tradizionale'),
            array('Hant', $this, true, 'it', 'tradizionale'),
            array('Hant', 123, true, 'it', 'tradizionale'),
            array('Hant', array(123), true, 'it', 'tradizionale'),
            array('Hant', Script::ALTERNATIVENAME_STANDALONE, true, 'it', 'han tradizionale'),
            array(static::MISSING_SCRIPTCODE, '', false, static::MISSING_SCRIPTCODE_FOR, ''),
            array(static::MISSING_SCRIPTCODE, '', true, static::MISSING_SCRIPTCODE_FOR, static::MISSING_SCRIPTCODE_ENGLISHNAME),
        );
    }

    /**
     * @dataProvider getScriptNameProvider
     *
     * @param string|mixed $scriptCode
     * @param string|mixed $preferredVariant
     * @param bool $fallbackToEnglish
     * @param string $locale
     * @param string $expectedResult
     */
    public function testGetScriptName($scriptCode, $preferredVariant, $fallbackToEnglish, $locale, $expectedResult)
    {
        $actualResult = Script::getScriptName($scriptCode, $preferredVariant, $fallbackToEnglish, $locale);
        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetAllScripts()
    {
        $scripts = Script::getAllScripts('', false, 'it');
        $this->assertSame('tradizionale', $scripts['Hant']);

        $scripts = Script::getAllScripts(Script::ALTERNATIVENAME_STANDALONE, false, 'it');
        $this->assertSame('han tradizionale', $scripts['Hant']);

        $scripts = Script::getAllScripts('', false, static::MISSING_SCRIPTCODE_FOR);
        $this->assertFalse(isset($scripts[static::MISSING_SCRIPTCODE]));
        $scripts = Script::getAllScripts('', true, static::MISSING_SCRIPTCODE_FOR);
        $this->assertTrue(isset($scripts[static::MISSING_SCRIPTCODE]));
    }
}
