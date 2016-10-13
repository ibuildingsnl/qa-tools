<?php
namespace Ibuildings\QaTools\Core\Build;

use Ibuildings\QaTools\Core\Assert\Assertion;

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
        Assertion::string(
            $targetIdentifier,
            sprintf('targetIdentifier should be string but is %s', gettype($targetIdentifier))
        );
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
