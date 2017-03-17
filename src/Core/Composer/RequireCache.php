<?php

namespace Ibuildings\QaTools\Core\Composer;

use Ibuildings\QaTools\Core\Exception\RuntimeException;

/**
 * This is a set that stores Composer configurations that resulted from requiring
 * a set of packages on an existing target configuration. It is used to optimize
 * performance in CliComposerProject.
 */
final class RequireCache
{
    /**
     * Composer configurations indexed by requiredPackages that were required.
     *
     * @var Configuration[]
     */
    private $configurations = [];

    /**
     * Stores the Composer newConfiguration that resulted from requiring the given set of
     * requiredPackages.
     *
     * @param Configuration $targetConfiguration The configuration on which the require was performed.
     * @param PackageSet    $requiredPackages
     * @param Configuration $newConfiguration The configuration that resulted from the require.
     * @return void
     */
    public function storeConfiguration(
        Configuration $targetConfiguration,
        PackageSet $requiredPackages,
        Configuration $newConfiguration
    ) {
        $key = $this->getKey($targetConfiguration, $requiredPackages);

        $this->configurations[$key] = $newConfiguration;
    }

    /**
     * @param Configuration $targetConfiguration The configuration on which the require was performed.
     * @param PackageSet    $requiredPackages
     * @return Configuration The configuration that would result from the require.
     * @throws RuntimeException When no configuration has been stored that describes the result of the requirement
     *     wishes.
     */
    public function getConfiguration(Configuration $targetConfiguration, PackageSet $requiredPackages)
    {
        $key = $this->getKey($targetConfiguration, $requiredPackages);

        if (!array_key_exists($key, $this->configurations)) {
            throw new RuntimeException(
                sprintf(
                    'No configuration is cached for the given requirement wishes "%s"',
                    join('", "', $requiredPackages->getDescriptors())
                )
            );
        }

        return $this->configurations[$key];
    }

    /**
     * @param Configuration $targetConfiguration The configuration on which the require was performed.
     * @param PackageSet    $requiredPackages
     * @return boolean Whether a configuration is stored that describes the result of the requirement wishes.
     * @throws RuntimeException When no configuration has been cached for the given requirements.
     */
    public function containsConfiguration(Configuration $targetConfiguration, PackageSet $requiredPackages)
    {
        $key = $this->getKey($targetConfiguration, $requiredPackages);

        return array_key_exists($key, $this->configurations);
    }

    /**
     * @param Configuration $targetConfiguration
     * @param PackageSet    $requiredPackages
     * @return string
     */
    private function getKey(Configuration $targetConfiguration, PackageSet $requiredPackages)
    {
        $packageDescriptors = join(',', $requiredPackages->getDescriptors());
        $composerJson = $targetConfiguration->getComposerJson();
        $composerLockJson = $targetConfiguration->hasLockedDependencies()
            ? $targetConfiguration->getComposerLockJson()
            : '';

        return sha1(join(':', [$packageDescriptors, $composerJson, $composerLockJson]));
    }
}
