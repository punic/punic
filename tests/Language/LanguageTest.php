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
            array('English', 'en', 'en'),
            array('inglese', 'en', 'it'),
            array('Italian', 'it', 'en'),
            array('italiano', 'it', 'it'),
            array('italiano (Italia)', 'it-it', 'it'),
            array('italiano (Italia)', 'it-it', 'it'),
            array('italiano (Svizzera)', 'it-CH', 'it'),
            array('Italian (Switzerland)', 'it-CH', 'en'),
            array('inglese americano (Stati Uniti)', 'en_US', 'it-IT'),
            array('American English (United States)', 'en_US', 'en_US'),
            array('Italian (World)', 'it-001', 'en'),
            array('Italian (Europe)', 'it-150', 'en'),
            array('italiano (Mondo)', 'it-001', 'it-001'),
            array('italiano (Europa)', 'it-150', 'it-150'),
            array('italiano (Europa)', 'it-150', 'it-CH'),
            array('Azerbaijani (Azerbaijan)', 'az-Latn-AZ', 'en'),
            array('Azerbaijani (Azerbaijan)', 'az-Cyrl-AZ', 'en'),
        );
    }

    /**
     * test getName
     * @dataProvider providerGetName
     */
    public function testGetName($result, $languageCode, $forLocale)
    {
        $this->assertSame(
            $result,
            Language::getName($languageCode, $forLocale)
        );
    }
}
