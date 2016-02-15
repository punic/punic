<?php

use \Punic\Comparer;

class ComparerTest extends PHPUnit_Framework_TestCase
{
    public function testSort()
    {
        $input = ['A', 'B'];
        $output = $input;
        $sorter = new Comparer();
        $result = $sorter->sort($output, false);
        $this->assertEquals($input, $output);
        $this->assertEquals($result, true);
    }

    public function testSortWithKeys()
    {
        $input = ['key1' => 'A', 'key2' => 'B'];
        $output = $input;
        $sorter = new Comparer();
        $result = $sorter->sort($output, true);
        $this->assertEquals($input, $output);
        $this->assertEquals($result, true);
    }

    public function testSortGerman()
    {
        $input = ['a', 'ä', 'b'];
        $output = $input;
        $sorter = new Comparer();
        $result = $sorter->sort($output);
        $this->assertEquals($input, $output);
        $this->assertEquals($result, true);
    }

    public function testCaseSensitive()
    {
        $input = ['a', 'A', 'b', 'B'];
        $output = $input;
        $sorter = new Comparer(null, true);
        $result = $sorter->sort($output, false);
        $this->assertEquals($input, $output);
        $this->assertEquals($result, true);
    }
}