<?php
namespace Punic\Exception;

/**
 * An exception raised when the \Punic\Calendar::toDateTime failed to create a \DateTime instance
 */
class NotConvertibleToDateTime extends \Punic\Exception
{
    protected $value;

    /**
     * Initializes the instance
     * @param mixed $value The valued that couldn't be converted to a \DateTime instance
     * @param \Exception $previous = null The previous exception used for the exception chaining
     */
    public function __construct($value, $previous = null)
    {
        $this->value = $value;
        $type = gettype($value);
        if($type === 'string') {
            $message = "'$value' couln't be converted to a \DateTime instance";
        }
        else {
            $message = "Can't convert a variable of kind '$type' to a \\DateTime instance";
        }
        parent::__construct($message, \Punic\Exception::NOT_CONVERTIBLE_TO_DATETIME, $previous);
    }

    /**
     * Retrieves the path to the data file
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

}
