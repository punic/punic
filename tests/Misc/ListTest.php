<?php
use \Punic\Misc;

class ListTest extends PHPUnit_Framework_TestCase
{

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

    public function testValueNotInListException()
	{
		$this->setExpectedException('\\Punic\\Exception\\ValueNotInList');
		Misc::joinUnits(array('One', 'Two'), 'invalid-width', 'en');	
	}
    
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
     */
    public function testFixCase($result, $string, $case)
    {
        $this->assertSame($result, \Punic\Misc::fixCase($string, $case));
    }
}
