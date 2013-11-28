<?php
namespace Ibuildings\QA\Tools\Common;

/**
 * Can check if version of a given command is installed
 *
 * Class CommandExistenceChecker
 * @package Ibuildings\QA\Tools\Common
 */
class CommandExistenceChecker
{
    /**
     * Requires a given command and optional version to be installed.
     *
     * @param string $name
     * @param string|null $minimalVersion
     * @throws \Exception if command is not installed
     */
    public function requireCommand($name, $minimalVersion = null)
    {
        $result = $this->commandExists($name, $message, $minimalVersion);
        if ($result !== true) {
            throw new \Exception($message);
        }
    }

    /**
     * Test if a given command is installed and optionally if the version is correct.
     *
     * @param string $name, may include a parameter to get the version for commands that do not use --version
     * @param string message return Message by reference
     * @param string|null $minimalVersion
     * @return bool
     */
    public function commandExists($name, &$message, $minimalVersion = null)
    {
        $versionParameter = '--version';
        $nameParts = explode(' ', $name);
        if (count($nameParts) > 1) {
            $commandName = $nameParts[0];
            $versionParameter = $nameParts[1];
        } else {
            $commandName = $name;
        }

        // Test if command is installed at all
        $returnVal = shell_exec("command -v $commandName");
        if (empty($returnVal)) {
            $message = "{$commandName} is not installed";
            return false;
        }

        // Optionally test if command version is correct
        if ($minimalVersion) {
            return $this->miniumVersionInstalled($commandName, $message, $minimalVersion, $versionParameter);
        }

        return true;
    }

    /**
     * Checks if the minium version is installed
     *
     * @param string $commandName
     * @param string $message Message by reference
     * @param string $minimalVersion
     * @param $versionParameter
     * @return bool
     */
    private function miniumVersionInstalled(
        $commandName,
        &$message,
        $minimalVersion,
        $versionParameter
    )
    {
        $installedVersionDesc = shell_exec("{$commandName} {$versionParameter}");
        $installedVersion = $this->parseVersionDesc($installedVersionDesc);
        $versionTooOld = version_compare($installedVersion, $minimalVersion) < 0;
        if ($versionTooOld) {
            $message = "Installed {$commandName} version '{$installedVersion}' is too old, at least version '{$minimalVersion}' is required'";
            return false;
        }

        return true;
    }

    /**
     * Parses version from version Description.
     *
     * Currently it works quite basic it looks for:
     *  - a string containing at least 2 digits separated by a string
     *  - prefixed by v or a space
     *  - optionally suffixed by anything but whitspace
     *
     * Examples: v2.3, 1.7.10.4, 5.4.6-1ubuntu1.4
     *
     * @param string $versionDesc
     * @return string
     */
    private function parseVersionDesc($versionDesc)
    {
        preg_match('/(\s|v)\d\.\d[^\s]*/', $versionDesc, $matches);
        return trim(array_shift($matches));
    }
}