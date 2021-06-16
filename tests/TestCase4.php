<?php

namespace Punic\Test;

abstract class TestCase4 extends TestCaseBase
{
    final public static function setupBeforeClass()
    {
        static::doSetUpBeforeClass();
    }

    final protected function setUp()
    {
        $this->doSetUp();
    }

    final protected function tearDown()
    {
        $this->doTearDown();
    }

    /**
     * @param string $pattern
     * @param string $string
     * @param string $message
     */
    public static function assertMatchRegExp($pattern, $string, $message = '')
    {
        static::assertRegExp($pattern, $string, $message);
    }

    /*
     * @param string $needle
     * @param string $haystack
     * @param string $message
     */
    /*
    public static function assertStringNotContainsString($needle, $haystack, $message = '')
    {
        static::assertNotContains($needle, $haystack, $message);
    }

    */
}
