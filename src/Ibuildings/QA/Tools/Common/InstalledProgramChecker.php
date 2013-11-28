<?php
namespace Ibuildings\QA\Tools\Common;

/**
 * Can check if version of a given program is installed
 *
 * Class InstalledProgramChecker
 * @package Ibuildings\QA\Tools\Common
 */
class InstalledProgramChecker
{
    /**
     * Requires a given program en version to be installed.
     *
     * @param string $name
     * @param string $minimalVersion
     * @throws \Exception if program is not installed
     */
    public function requireProgram($name, $minimalVersion)
    {
        $result = $this->testIfProgramIsInstalled($name, $minimalVersion);
        if ($result !== true) {
            throw new \Exception($result);
        }
    }

    /**
     * Test if a given program is installed and if the version is correct.
     *
     * @param string $name, may include a parameter to get the version for programs that do not use --version
     * @param string $minimalVersion
     * @return bool|string true if installed else a message
     */
    public function testIfProgramIsInstalled($name, $minimalVersion)
    {

        $versionParameter = '--version';
        $nameParts = explode(' ', $name);
        if (count($nameParts) > 1) {
            $programName = $nameParts[0];
            $versionParameter = $nameParts[1];
        } else {
            $programName = $name;
        }

        $installedVersionDesc = shell_exec("{$programName} {$versionParameter}");

        if (is_null($installedVersionDesc)) {
            return "{$programName} is not installed, at least version '{$minimalVersion}' is required'";
        }

        $installedVersion = $this->parseVersionDesc($installedVersionDesc);
        $versionTooOld = version_compare($installedVersion, $minimalVersion) < 0;
        if (!empty($returnVal) || $versionTooOld) {
            return "Installed {$programName} version '{$installedVersion}' is too old, at least version '{$minimalVersion}' is required'";
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