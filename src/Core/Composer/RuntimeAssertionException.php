<?php

namespace Ibuildings\QaTools\Core\Composer;

use Assert\AssertionFailedException;
use Ibuildings\QaTools\Core\Exception\RuntimeException as QaToolsRuntimeException;

class RuntimeAssertionException extends QaToolsRuntimeException implements AssertionFailedException
{
    private $propertyPath;
    private $value;
    private $constraints;

    /**
     * @param string $message
     * @param mixed  $value
     * @param null   $propertyPath
     * @param array  $constraints
     */
    public function __construct($message, $value, $propertyPath = null, array $constraints = array())
    {
        parent::__construct($message, 0);

        $this->propertyPath = $propertyPath;
        $this->value = $value;
        $this->constraints = $constraints;
    }

    /**
     * User controlled way to define a sub-property causing
     * the failure of a currently asserted objects.
     *
     * Useful to transport information about the nature of the error
     * back to higher layers.
     *
     * @return string|null
     */
    public function getPropertyPath()
    {
        return $this->propertyPath;
    }

    /**
     * Get the value that caused the assertion to fail.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Get the constraints that applied to the failed assertion.
     *
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }
}
