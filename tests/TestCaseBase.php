<?php

namespace Punic\Test;

use Exception;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

abstract class TestCaseBase extends PHPUnitTestCase
{
    /**
     * Narrow No-Break Space (NNBSP).
     * Placeholder: <NNBSP>.
     *
     * @see https://www.compart.com/en/unicode/U+202F
     *
     * @var string
     */
    const UTF8_NARROW_NOBREAK_SPACE = "\xE2\x80\xAF";

    /**
     * Thin Space.
     * Placeholder: <THINSP>.
     *
     * @see https://www.compart.com/en/unicode/U+2009
     *
     * @var string
     */
    const UTF8_THIN_SPACE_CHAR = "\xE2\x80\x89";

    /**
     * En Dash.
     * Placeholder: <ENDASH>.
     *
     * @see https://www.compart.com/en/unicode/U+2013
     *
     * @var string
     */
    const UTF8_ENDASH = "\xE2\x80\x93";

    /**
     * This method is called before the first test of this test class is run.
     * Override it instead of setUpBeforeClass().
     */
    protected static function doSetUpBeforeClass()
    {
    }

    /**
     * This method is called before each test.
     * Override it instead of doSetUp().
     */
    protected function doSetUp()
    {
    }

    /**
     * This method is called after each test.
     * Override it instead of tearUp().
     */
    protected function doTearDown()
    {
    }

    /**
     * @param string $exception
     * @param string $message
     * @param int|null $code
     */
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
                    throw new Exception('Invalid $exception argument in ' . __FUNCTION__);
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

    /**
     * @param string $string
     *
     * @return string
     */
    protected static function unicodeString($string)
    {
        return strtr(
            $string,
            array(
                '<NNBSP>' => self::UTF8_NARROW_NOBREAK_SPACE,
                '<THINSP>' => self::UTF8_THIN_SPACE_CHAR,
                '<ENDASH>' => self::UTF8_ENDASH,
            )
        );
    }
}
