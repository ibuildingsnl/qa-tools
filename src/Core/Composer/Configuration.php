<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class Configuration
{
    /**
     * @var string
     */
    private $composerJson;

    /**
     * @var string|null
     */
    private $composerLockJson;

    /**
     * @param string $composerJson
     * @param string $composerLockJson
     * @return Configuration
     */
    public static function withLockedDependencies($composerJson, $composerLockJson)
    {
        Assertion::string($composerJson, 'Composer JSON ought to be a string, got "%s" of type "%s"');
        Assertion::string($composerLockJson, 'Composer lock JSON ought to be a string, got "%s" of type "%s"');

        return new Configuration($composerJson, $composerLockJson);
    }

    /**
     * @param string $composerJson
     * @return Configuration
     */
    public static function withoutLockedDependencies($composerJson)
    {
        Assertion::string($composerJson, 'Composer JSON ought to be a string, got "%s" of type "%s"');

        return new Configuration($composerJson, null);
    }

    /**
     * @param string $composerJson
     * @param string|null $composerLockJson
     */
    private function __construct($composerJson, $composerLockJson)
    {
        $this->composerJson = $composerJson;
        $this->composerLockJson = $composerLockJson;
    }

    /**
     * @return bool
     */
    public function hasLockedDependencies()
    {
        return $this->composerLockJson !== null;
    }

    /**
     * @return string
     */
    public function getComposerJson()
    {
        return $this->composerJson;
    }

    /**
     * @return string|null
     */
    public function getComposerLockJson()
    {
        return $this->composerLockJson;
    }

    /**
     * @param Configuration $configuration
     * @return boolean
     */
    public function equals(Configuration $configuration)
    {
        return $this->composerJson === $configuration->composerJson
            && $this->composerLockJson === $configuration->composerLockJson;
    }
}
