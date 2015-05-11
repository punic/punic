<?php

use \Punic\Misc;

class TextDirectionsTest extends PHPUnit_Framework_TestCase
{
    public function providerCharacterOrder()
    {
        return array(
            array('en', 'left-to-right'),
            array('en_US', 'left-to-right'),
            array('it', 'left-to-right'),
            array('ar', 'right-to-left'),
        );
    }

    /**
     * @dataProvider providerCharacterOrder
     */
    public function testCharacterOrder($locale, $expectedDirection)
    {
        $this->assertSame($expectedDirection, Misc::getCharacterOrder($locale));
    }

    public function testCharacterOrderDefault()
    {
        \Punic\Data::setDefaultLocale('ar');
        $this->assertSame('right-to-left', Misc::getCharacterOrder());
        \Punic\Data::setDefaultLocale('en_US');
        $this->assertSame('left-to-right', Misc::getCharacterOrder());
    }

    public function providerLineOrder()
    {
        return array(
            array('en', 'top-to-bottom'),
            array('en_US', 'top-to-bottom'),
            array('it', 'top-to-bottom'),
            array('ar', 'top-to-bottom'),
        );
    }

    /**
     * @dataProvider providerLineOrder
     */
    public function testLineOrder($locale, $expectedDirection)
    {
        $this->assertSame($expectedDirection, Misc::getLineOrder($locale));
    }

    public function testLineOrderDefault()
    {
        \Punic\Data::setDefaultLocale('ar');
        $this->assertSame('top-to-bottom', Misc::getLineOrder());
        \Punic\Data::setDefaultLocale('en_US');
        $this->assertSame('top-to-bottom', Misc::getLineOrder());
    }
}
