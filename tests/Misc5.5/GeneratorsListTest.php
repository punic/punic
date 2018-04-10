<?php

use PHPUnit\Framework\TestCase;
use Punic\Misc;

class GeneratorsListTest extends TestCase
{
    public function testJoinWithGenerators()
    {
        $this->assertSame('Uno, due o tre', Misc::joinOr($this->generatorProvider(), '', 'it'));
    }

    /**
     * @return Generator
     */
    protected function generatorProvider()
    {
        yield 'Uno';
        yield 'due';
        yield 'tre';
    }
}
