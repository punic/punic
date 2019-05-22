<?php

namespace Punic\Test\Misc;

use Punic\Data;
use Punic\Misc;
use Punic\Test\TestCase;

class ListTest extends TestCase
{
    public function testJoin()
    {
        $this->assertSame(
            '',
            Misc::join(false, 'en')
        );
        $this->assertSame(
            'One and Two',
            Misc::join(array('One', 'Two'), 'en')
        );
        $this->assertSame(
            'Uno e due',
            Misc::join(array('Uno', 'due'), 'it')
        );
    }

    public function testJoinAnd()
    {
        $this->assertSame(
            '',
            Misc::joinAnd(false, '', 'en')
        );
        $this->assertSame(
            '',
            Misc::joinAnd('', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::joinAnd(array(), '', 'en')
        );
        $this->assertSame(
            'One',
            Misc::joinAnd(array('One'), '', 'en')
        );
        $this->assertSame(
            'One and Two',
            Misc::joinAnd(array('One', 'Two'), '', 'en')
        );
        $this->assertSame(
            'One, Two, and Three',
            Misc::joinAnd(array('One', 'Two', 'Three'), '', 'en')
        );
        $this->assertContains(
            Misc::joinAnd(array('One', 'Two', 'Three'), 'short', 'en'),
            array('One, Two, and Three', 'One, Two, & Three')
        );
        $this->assertContains(
            Misc::joinAnd(array('One', 'Two', 'Three'), 'narrow', 'en'),
            array('One, Two, and Three', 'One, Two, Three')
        );
        $this->assertSame(
            'One, Two and Three',
            Misc::joinAnd(array('One', 'Two', 'Three'), '', 'en-GB')
        );
        $this->assertSame(
            'One, Two, Three, and Four',
            Misc::joinAnd(array('One', 'Two', 'Three', 'Four'), '', 'en')
        );
        $this->assertSame(
            'One, Two, Three, Four, and 5',
            Misc::joinAnd(array('One', 'Two', 'Three', 'Four', 5), '', 'en')
        );
        $this->assertSame(
            'Uno',
            Misc::joinAnd(array('Uno'), '', 'it')
        );
        $this->assertSame(
            'Uno e due',
            Misc::joinAnd(array('Uno', 'due'), '', 'it')
        );
        $this->assertSame(
            'Uno, due e tre',
            Misc::joinAnd(array('Uno', 'due', 'tre'), '', 'it')
        );
        $this->assertSame(
            'Uno, due, tre e quattro',
            Misc::joinAnd(array('Uno', 'due', 'tre', 'quattro'), '', 'it')
        );
        $this->assertSame(
            'Uno, due, tre, quattro e 5',
            Misc::joinAnd(array('Uno', 'due', 'tre', 'quattro', 5), '', 'it')
        );

        Data::setDefaultLocale('de');
        $this->assertSame(
            'Eins und zwei',
            Misc::joinAnd(array('Eins', 'zwei'))
        );
    }

    public function testJoinOr()
    {
        $this->assertSame(
            '',
            Misc::joinOr(false, '', 'en')
        );
        $this->assertSame(
            '',
            Misc::joinOr('', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::joinOr(array(), '', 'en')
        );
        $this->assertSame(
            'One',
            Misc::joinOr(array('One'), '', 'en')
        );
        $this->assertSame(
            'One or Two',
            Misc::joinOr(array('One', 'Two'), '', 'en')
        );
        $this->assertSame(
            'One, Two, or Three',
            Misc::joinOr(array('One', 'Two', 'Three'), '', 'en')
        );
        $this->assertSame(
            'One, Two, or Three',
            Misc::joinOr(array('One', 'Two', 'Three'), 'short', 'en')
        );
        $this->assertSame(
            'One, Two, or Three',
            Misc::joinOr(array('One', 'Two', 'Three'), 'narrow', 'en')
        );
        $this->assertSame(
            'One, Two or Three',
            Misc::joinOr(array('One', 'Two', 'Three'), '', 'en-GB')
        );
        $this->assertSame(
            'One, Two, Three, or Four',
            Misc::joinOr(array('One', 'Two', 'Three', 'Four'), '', 'en')
        );
        $this->assertSame(
            'One, Two, Three, Four, or 5',
            Misc::joinOr(array('One', 'Two', 'Three', 'Four', 5), '', 'en')
        );
        $this->assertSame(
            'Uno',
            Misc::joinOr(array('Uno'), '', 'it')
        );
        $this->assertSame(
            'Uno o due',
            Misc::joinOr(array('Uno', 'due'), '', 'it')
        );
        $this->assertSame(
            'Uno, due o tre',
            Misc::joinOr(array('Uno', 'due', 'tre'), '', 'it')
        );
        $this->assertSame(
            'Uno, due, tre o quattro',
            Misc::joinOr(array('Uno', 'due', 'tre', 'quattro'), '', 'it')
        );
        $this->assertSame(
            'Uno, due, tre, quattro o 5',
            Misc::joinOr(array('Uno', 'due', 'tre', 'quattro', 5), '', 'it')
        );

        Data::setDefaultLocale('de');
        $this->assertSame(
            'Eins oder zwei',
            Misc::joinOr(array('Eins', 'zwei'))
        );
    }

    public function testJoinUnits()
    {
        $this->assertSame(
            '',
            Misc::joinUnits(false, '', 'en')
        );
        $this->assertSame(
            '',
            Misc::joinUnits('', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::joinUnits(array(), '', 'en')
        );
        $this->assertSame(
            'One',
            Misc::joinUnits(array('One'), '', 'en')
        );
        $this->assertSame(
            'One, Two',
            Misc::joinUnits(array('One', 'Two'), '', 'en')
        );
        $this->assertSame(
            'One, Two, Three',
            Misc::joinUnits(array('One', 'Two', 'Three'), '', 'en')
        );
        $this->assertSame(
            'One, Two, Three',
            Misc::joinUnits(array('One', 'Two', 'Three'), 'short', 'en')
        );
        $this->assertSame(
            'One Two Three',
            Misc::joinUnits(array('One', 'Two', 'Three'), 'narrow', 'en')
        );
        $this->assertSame(
            'One, Two, Three, Four',
            Misc::joinUnits(array('One', 'Two', 'Three', 'Four'), '', 'en')
        );
        $this->assertSame(
            'One, Two, Three, Four, 5',
            Misc::joinUnits(array('One', 'Two', 'Three', 'Four', 5), '', 'en')
        );
        $this->assertSame(
            'Uno',
            Misc::joinUnits(array('Uno'), '', 'it')
        );
        $this->assertSame(
            'Uno e due',
            Misc::joinUnits(array('Uno', 'due'), '', 'it')
        );
        $this->assertSame(
            'Uno, due e tre',
            Misc::joinUnits(array('Uno', 'due', 'tre'), '', 'it')
        );
        $this->assertSame(
            'Uno, due e tre',
            Misc::joinUnits(array('Uno', 'due', 'tre'), 'short', 'it')
        );
        $this->assertSame(
            'Uno due tre',
            Misc::joinUnits(array('Uno', 'due', 'tre'), 'narrow', 'it')
        );
        $this->assertSame(
            'Uno, due, tre e quattro',
            Misc::joinUnits(array('Uno', 'due', 'tre', 'quattro'), '', 'it')
        );
        $this->assertSame(
            'Uno, due, tre, quattro e 5',
            Misc::joinUnits(array('Uno', 'due', 'tre', 'quattro', 5), '', 'it')
        );
    }

    public function testInvalidWidthException()
    {
        $this->setExpectedException('Punic\\Exception\\ValueNotInList', "'invalid-width' is not valid. Acceptable values are: '', 'short', 'narrow'");
        Misc::joinAnd(array('One', 'Two'), 'invalid-width', 'en');
    }

    /**
     * @return array
     */
    public function provideFixCase()
    {
        return array(
            array('Test', 'test', 'titlecase-words'),
            array('Test Test', 'test test', 'titlecase-words'),
            array('TEST TEST', 'TEST TEST', 'titlecase-words'),
            array('A', 'a', 'titlecase-words'),
            array('Test', 'test', 'titlecase-firstword'),
            array('Test test', 'test test', 'titlecase-firstword'),
            array('TEST TEST', 'TEST TEST', 'titlecase-firstword'),
            array('A', 'a', 'titlecase-firstword'),
            array('a', 'a', 'lowercase-words'),
            array('a', 'A', 'lowercase-words'),
            array('test test', 'Test test', 'lowercase-words'),
        );
    }

    /**
     * @dataProvider provideFixCase
     *
     * @param string $result
     * @param string $string
     * @param string $case
     */
    public function testFixCase($result, $string, $case)
    {
        $this->assertSame($result, Misc::fixCase($string, $case));
    }
}
