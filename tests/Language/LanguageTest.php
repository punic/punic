<?php

use \Punic\Language;

class LanguageTest extends PHPUnit_Framework_TestCase
{
    public function testGetAll()
    {
        $languages = Language::getAll(false, false);
        $this->assertArrayHasKey('en', $languages);
        $this->assertArrayHasKey('en-US', $languages);
        $this->assertArrayHasKey('zh-Hans', $languages);

        $languages = Language::getAll(false, true);
        $this->assertArrayHasKey('en', $languages);
        $this->assertArrayHasKey('en-US', $languages);
        $this->assertArrayNotHasKey('zh-Hans', $languages);

        $languages = Language::getAll(true, false);
        $this->assertArrayHasKey('en', $languages);
        $this->assertArrayNotHasKey('en-US', $languages);
        $this->assertArrayHasKey('zh-Hans', $languages);

        $languages = Language::getAll(true, true);
        $this->assertArrayHasKey('en', $languages);
        $this->assertArrayNotHasKey('en-US', $languages);
        $this->assertArrayNotHasKey('zh-Hans', $languages);

        $languages = Language::getAll(false, false, 'en');
        $this->assertSame('English', $languages['en']);

        $languages = Language::getAll(false, false, 'it');
        $this->assertSame('inglese', $languages['en']);
    }

    public function providerGetName()
    {
        return array(
            array('English', 'en', 'en', false),
            array('inglese', 'en', 'it', false),
            array('Italian', 'it', 'en', false),
            array('italiano', 'it', 'it', false),
            array('italiano (Italia)', 'it-it', 'it', false),
            array('italiano (Italia)', 'it-it', 'it', false),
            array('italiano (Svizzera)', 'it-CH', 'it', false),
            array('Italian (Switzerland)', 'it-CH', 'en', false),
            array('inglese (Stati Uniti)', 'en_US', 'it-IT', false),
            array('inglese americano (Stati Uniti)', 'en_US', 'it-IT', true),
            array('English (United States)', 'en_US', 'en_US', false),
            array('American English (United States)', 'en_US', 'en_US', true),
            array('Italian (World)', 'it-001', 'en', false),
            array('Italian (Europe)', 'it-150', 'en', false),
            array('italiano (Mondo)', 'it-001', 'it-001', false),
            array('italiano (Europa)', 'it-150', 'it-150', false),
            array('italiano (Europa)', 'it-150', 'it-CH', false),
            array('Azerbaijani (Azerbaijan)', 'az-Latn-AZ', 'en', false),
            array('Azerbaijani (Azerbaijan)', 'az-Cyrl-AZ', 'en', false),
        );
    }

    /**
     * test getName.
     *
     * @dataProvider providerGetName
     */
    public function testGetName($result, $languageCode, $forLocale, $allowCompoundNames)
    {
        $this->assertSame(
            $result,
            Language::getName($languageCode, $forLocale, $allowCompoundNames)
        );
    }
}
