<?php
namespace Ibuildings\QaTools\Core\Build;

final class Target
{
    /**
     * @var string
     */
    private $targetIdentifier;

    /**
     * @param string $targetIdentifier
     */
    private function __construct($targetIdentifier)
    {
        $this->targetIdentifier = $targetIdentifier;
    }

    /**
     * @return Target
     */
    public static function build()
    {
        return new self('build');
    }

    /**
     * @return Target
     */
    public static function preCommit()
    {
        return new self('precommit');
    }

    /**
     * @return string
     */
    public function getTargetIdentifier()
    {
        return $this->targetIdentifier;
    }

    /**
     * @param Target $other
     * @return bool
     */
    public function equals(Target $other)
    {
        return $this->targetIdentifier === $other->targetIdentifier;
    }
}
