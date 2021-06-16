<?php

namespace Punic\Test;

abstract class TestCase9 extends TestCase7
{
    public static function assertMatchRegExp(string $pattern, string $string, string $message = ''): void
    {
        static::assertMatchesRegularExpression($pattern, $string, $message);
    }
}
