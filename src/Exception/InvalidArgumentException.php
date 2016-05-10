<?php

namespace Ibuildings\QaTools\Exception;

use Assert\InvalidArgumentException as InvalidAssertionException;

class InvalidArgumentException extends InvalidAssertionException
{
    public function __construct($message, $code, $propertyPath = null, $value, array $constraints = array())
    {
        if ($propertyPath !== null && strpos($message, $propertyPath) === false) {
            $message = sprintf('Invalid argument given for "%s": %s', $propertyPath, $message);
        }
        parent::__construct($message, $code, $propertyPath, $value, $constraints);
    }
}
