<?php
namespace Punic;

class Exception extends \Exception
{
    const INVALID_LOCALE = 10001;
    const INVALID_DATAFILE = 10002;
    const DATA_FOLDER_NOT_FOUND = 10003;
    /**
     * Initializes the instance
     * @param string $message
     * @param int $code
     * @param \Exception $previous The previous exception used for the exception chaining
     */
    public function __construct($message, $code = null, $previous = null)
    {
        parent::__construct($message, $code ? $code : 1, $previous);
    }
}
