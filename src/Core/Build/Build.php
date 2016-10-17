<?php
namespace Ibuildings\QaTools\Core\Build;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class Build
{
    /**
     * @var string
     */
    private $buildIdentifier;

    /**
     * @return Build
     */
    public static function main()
    {
        return new self('main');
    }

    /**
     * @return Build
     */
    public static function preCommit()
    {
        return new self('precommit');
    }

    /**
     * @param string $targetIdentifier
     */
    private function __construct($targetIdentifier)
    {
        Assertion::string(
            $targetIdentifier,
            sprintf('targetIdentifier should be string but is %s', gettype($targetIdentifier))
        );
        $this->buildIdentifier = $targetIdentifier;
    }

    /**
     * @return string
     */
    public function getBuildIdentifier()
    {
        return $this->buildIdentifier;
    }

    /**
     * @param Build $other
     * @return bool
     */
    public function equals(Build $other)
    {
        return $this->buildIdentifier === $other->buildIdentifier;
    }
}
