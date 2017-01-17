<?php

namespace Ibuildings\QaTools\Core\Npm;

use Exception;

final class RuntimeException extends \RuntimeException
{
    /** @var string */
    private $cause;

    /**
     * @param string         $message
     * @param string         $cause A detailed, ideally human-readable, explanation of the cause of this exception.
     * @param Exception|null $previous
     */
    public function __construct($message, $cause, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);

        $this->cause = $cause;
    }

    /**
     * Returns a detailed, ideally human-readable, explanation of the cause of this exception.
     *
     * @return string
     */
    public function getCause()
    {
        return $this->cause;
    }
}
