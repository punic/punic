<?php
use \Punic\Unit;

class UnitTest extends PHPUnit_Framework_TestCase
{

    public function providerFormat()
    {
        return array(
            array(
                '1 millisecond',
                array(1, 'millisecond', 'long', 'en')
            )
        );
    }

    /**
     * test format
     * @dataProvider providerFormat
     */
    public function testFormat($result, $parameters)
    {
        $this->assertSame(
            $result,
            Unit::format($parameters[0], $parameters[1], $parameters[2], $parameters[3])
        );
    }

}
