<?php

namespace Punic\Test\Misc;

use Punic\Data;
use Punic\Misc;
use Punic\Test\TestCase;

class TextDirectionsTest extends TestCase
{
    /**
     * @return array
     */
    public function provideCharacterOrder()
    {
        return array(
            array('en', 'left-to-right'),
            array('en_US', 'left-to-right'),
            array('it', 'left-to-right'),
            array('ar', 'right-to-left'),
        );
    }

    /**
     * @dataProvider provideCharacterOrder
     *
     * @param string $locale
     * @param string $expectedDirection
     */
    public function testCharacterOrder($locale, $expectedDirection)
    {
        $this->assertSame($expectedDirection, Misc::getCharacterOrder($locale));
    }

    public function testCharacterOrderDefault()
    {
        Data::setDefaultLocale('ar');
        $this->assertSame('right-to-left', Misc::getCharacterOrder());
        Data::setDefaultLocale('en_US');
        $this->assertSame('left-to-right', Misc::getCharacterOrder());
    }

    /**
     * @return array
     */
    public function provideLineOrder()
    {
        return array(
            array('en', 'top-to-bottom'),
            array('en_US', 'top-to-bottom'),
            array('it', 'top-to-bottom'),
            array('ar', 'top-to-bottom'),
        );
    }

    /**
     * @dataProvider provideLineOrder
     *
     * @param string $locale
     * @param string $expectedDirection
     */
    public function testLineOrder($locale, $expectedDirection)
    {
        $this->assertSame($expectedDirection, Misc::getLineOrder($locale));
    }

    public function testLineOrderDefault()
    {
        Data::setDefaultLocale('ar');
        $this->assertSame('top-to-bottom', Misc::getLineOrder());
        Data::setDefaultLocale('en_US');
        $this->assertSame('top-to-bottom', Misc::getLineOrder());
    }
}
