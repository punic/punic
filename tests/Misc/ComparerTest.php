<?php

namespace Punic\Test\Misc;

use Punic\Comparer;
use Punic\Test\TestCase;

class ComparerTest extends TestCase
{
    /**
     * @dataProvider provideCompareData
     *
     * @param array $input
     * @param array $expected
     */
    public function testCompare(array $input, array $expected)
    {
        $cmp = new Comparer();
        $cmp->sort($input);
        $this->assertSame($expected, $input);
    }

    /**
     * @return array
     */
    public function provideCompareData()
    {
        return array(
            array(array('A', 'B'), array('A', 'B')),
            array(array('B', 'A'), array('A', 'B')),
            array(array('3A', '3B'), array('3A', '3B')),
            array(array('3B', '3A'), array('3A', '3B')),
        );
    }
}
