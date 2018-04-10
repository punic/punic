<?php

use Punic\Misc;
use PHPUnit\Framework\TestCase;

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
