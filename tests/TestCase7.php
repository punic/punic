<?php

namespace Punic\Test;

abstract class TestCase7 extends TestCaseBase
{
    final public static function setupBeforeClass(): void
    {
        static::doSetUpBeforeClass();
    }

    final protected function setUp(): void
    {
        $this->doSetUp();
    }

    final protected function tearDown(): void
    {
        $this->doTearDown();
    }

    public static function assertMatchRegExp(string $pattern, string $string, string $message = ''): void
    {
        static::assertRegExp($pattern, $string, $message);
    }
}
