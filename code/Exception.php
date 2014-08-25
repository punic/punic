<?php
namespace Punic;

/**
 * An exception raised by and associated to Punic
 */
class Exception extends \Exception
{
    /**
     * Exception code for the \Punic\Exception\InvalidLocale exception
     * @var int
     */
    const INVALID_LOCALE = 10001;

    /**
     * Exception code for the \Punic\Exception\InvalidDataFile exception
     * @var int
     */
    const INVALID_DATAFILE = 10002;

    /**
     * Exception code for the \Punic\Exception\DataFolderNotFound exception
     * @var int
     */
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
