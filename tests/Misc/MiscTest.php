<?php
use \Punic\Misc;

class MishTest extends PHPUnit_Framework_TestCase
{

    public function testImplode()
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
            'Uno, due, e tre',
            Misc::join(array('Uno', 'due', 'tre'), 'it')
        );
        $this->assertSame(
            'Uno, due, tre, e quattro',
            Misc::join(array('Uno', 'due', 'tre', 'quattro'), 'it')
        );
        $this->assertSame(
            'Uno, due, tre, quattro, e 5',
            Misc::join(array('Uno', 'due', 'tre', 'quattro', 5), 'it')
        );
    }

}
