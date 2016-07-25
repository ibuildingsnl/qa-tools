<?php

namespace Ibuildings\QaTools\Core\Exception;

use Assert\InvalidArgumentException as InvalidAssertionException;

class InvalidArgumentException extends InvalidAssertionException
{
    public function __construct($message, $code = null, $propertyPath = null, $value = null, array $constraints = [])
    {
        if ($propertyPath !== null && strpos($message, $propertyPath) === false) {
            $message = sprintf('Invalid argument given for "%s": %s', $propertyPath, $message);
        }
        parent::__construct($message, $code, $propertyPath, $value, $constraints);
    }
}
