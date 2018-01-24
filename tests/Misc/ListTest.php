<?php

use Punic\Misc;

class ListTest extends PHPUnit_Framework_TestCase
{
    public function testList()
    {
        $this->assertSame(
            '',
            Misc::list(false, 'standard', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::list('', 'standard', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::list(array(), 'standard', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::list(false, 'or', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::list('', 'or', '', 'en')
        );
        $this->assertSame(
            '',
            Misc::list(array(), 'or', '', 'en')
        );
        $this->assertSame(
            'One',
            Misc::list(array('One'), 'standard', '', 'en')
        );
        $this->assertSame(
            'One',
            Misc::list(array('One'), 'or', '', 'en')
        );
        $this->assertSame(
            'One and Two',
            Misc::list(array('One', 'Two'), 'standard', '', 'en')
        );
        $this->assertSame(
            'One and Two',
            Misc::list(array('One', 'Two'), 'standard', 'short', 'en')
        );
        $this->assertSame(
            'One and Two',
            Misc::list(array('One', 'Two'), 'standard', 'narrow', 'en')
        );
        $this->assertSame(
            'One or Two',
            Misc::list(array('One', 'Two'), 'or', '', 'en')
        );
        $this->assertSame(
            'One, Two, and Three',
            Misc::list(array('One', 'Two', 'Three'), 'standard', '', 'en')
        );
        $this->assertSame(
            'One, Two and Three',
            Misc::list(array('One', 'Two', 'Three'), 'standard', '', 'en_GB')
        );
        $this->assertSame(
            'One, Two, or Three',
            Misc::list(array('One', 'Two', 'Three'), 'or', '', 'en')
        );
        $this->assertSame(
            'One, Two, Three, and Four',
            Misc::list(array('One', 'Two', 'Three', 'Four'), 'standard', '', 'en')
        );
        $this->assertSame(
            'One, Two, Three, or Four',
            Misc::list(array('One', 'Two', 'Three', 'Four'), 'or', '', 'en')
        );
        $this->assertSame(
            'One, Two, Three, Four, and 5',
            Misc::list(array('One', 'Two', 'Three', 'Four', 5), 'standard', '', 'en')
        );
        $this->assertSame(
            'Uno',
            Misc::list(array('Uno'), 'standard', '', 'it')
        );
        $this->assertSame(
            'Uno e due',
            Misc::list(array('Uno', 'due'), 'standard', '', 'it')
        );
        $this->assertSame(
            'Uno o due',
            Misc::list(array('Uno', 'due'), 'or', '', 'it')
        );
        $this->assertSame(
            'Uno, due e tre',
            Misc::list(array('Uno', 'due', 'tre'), 'standard', '', 'it')
        );
        $this->assertSame(
            'Uno, due, tre e quattro',
            Misc::list(array('Uno', 'due', 'tre', 'quattro'), 'standard', '', 'it')
        );
        $this->assertSame(
            'Uno, due, tre, quattro e 5',
            Misc::list(array('Uno', 'due', 'tre', 'quattro', 5), 'standard', '', 'it')
        );

        $this->assertSame(
            '1 ft, 3 in',
            Misc::list(array('1 ft', '3 in'), 'unit', '', 'en')
        );
        $this->assertSame(
            '1 ft, 3 in',
            Misc::list(array('1 ft', '3 in'), 'unit', 'short', 'en')
        );
        $this->assertSame(
            '1 ft 3 in',
            Misc::list(array('1 ft', '3 in'), 'unit', 'narrow', 'en')
        );
        $this->assertSame(
            '1 piede e 3 pollici',
            Misc::list(array('1 piede', '3 pollici'), 'unit', '', 'it')
        );
        $this->assertSame(
            '1 piede e 3 pollici',
            Misc::list(array('1 piede', '3 pollici'), 'unit', 'short', 'it')
        );
        $this->assertSame(
            '1 piede 3 pollici',
            Misc::list(array('1 piede', '3 pollici'), 'unit', 'narrow', 'it')
        );
    }

    public function testJoin()
    {
        $this->assertSame(
            '',
            Misc::join(false, 'en')
        );
        $this->assertSame(
            '',
            Misc::join('', 'en')
        );
        $this->assertSame(
            '',
            Misc::join(array(), 'en')
        );
        $this->assertSame(
            'One',
            Misc::join(array('One'), 'en')
        );
        $this->assertSame(
            'One and Two',
            Misc::join(array('One', 'Two'), 'en')
        );
        $this->assertSame(
            'One, Two, and Three',
            Misc::join(array('One', 'Two', 'Three'), 'en')
        );
        $this->assertSame(
            'One, Two, Three, and Four',
            Misc::join(array('One', 'Two', 'Three', 'Four'), 'en')
        );
        $this->assertSame(
            'One, Two, Three, Four, and 5',
            Misc::join(array('One', 'Two', 'Three', 'Four', 5), 'en')
        );
        $this->assertSame(
            'Uno',
            Misc::join(array('Uno'), 'it')
        );
        $this->assertSame(
            'Uno e due',
            Misc::join(array('Uno', 'due'), 'it')
        );
        $this->assertSame(
            'Uno, due e tre',
            Misc::join(array('Uno', 'due', 'tre'), 'it')
        );
        $this->assertSame(
            'Uno, due, tre e quattro',
            Misc::join(array('Uno', 'due', 'tre', 'quattro'), 'it')
        );
        $this->assertSame(
            'Uno, due, tre, quattro e 5',
            Misc::join(array('Uno', 'due', 'tre', 'quattro', 5), 'it')
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

    public function testInvalidType()
    {
        $this->setExpectedException('\\Punic\\Exception\\ValueNotInList', "'invalid-type' is not valid. Acceptable values are: 'standard', 'or', 'unit");
        Misc::list(array('One', 'Two'), 'invalid-type', '', 'en');
    }

    public function testInvalidWidthException()
    {
        $this->setExpectedException('\\Punic\\Exception\\ValueNotInList', "'invalid-width' is not valid. Acceptable values are: '', 'short', 'narrow'");
        Misc::list(array('One', 'Two'), '', 'invalid-width', 'en');
    }

    /**
     * @return array
     */
    public function providerFixCase()
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
     * @dataProvider providerFixCase
     *
     * @param string $result
     * @param string $string
     * @param string $case
     */
    public function testFixCase($result, $string, $case)
    {
        $this->assertSame($result, \Punic\Misc::fixCase($string, $case));
    }
}
