<?php

namespace Punic\Test;

use Exception;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCase extends PHPUnitTestCase
{
    public function setExpectedException($exception, $message = '', $code = null)
    {
        if (!method_exists($this, 'expectException')) {
            parent::setExpectedException($exception, $message, $code);

            return;
        }
        $this->expectException($exception);
        if (func_num_args() >= 2) {
            if ($message !== null) {
                if (!is_string($message)) {
                    throw new Exception('Invalid $exception argument in '.__FUNCTION__);
                }
                $this->expectExceptionMessage($message);
            }
            if (func_num_args() >= 3) {
                if ($code !== null) {
                    $this->expectExceptionCode(null);
                }
            }
        }
    }
}
