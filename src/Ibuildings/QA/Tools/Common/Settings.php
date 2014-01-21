<?php

namespace Ibuildings\QA\Tools\Common;

use Camspiers\JsonPretty\JsonPretty;

class Settings extends \ArrayObject
{
    /**
     * @var string
     */
    protected $filename = 'settings.json';

    /**
     * Instantiate the object.
     *
     * @param string $baseDir
     * @param string $packageBaseDir
     * @throws \Exception
     */
    public function __construct($baseDir, $packageBaseDir)
    {
        if (!is_dir($packageBaseDir)) {
            throw new \Exception('Cannot find vendor package dir:' . $packageBaseDir);
        }

        if (!is_dir($baseDir)) {
            throw new \Exception('Cannot find project base dir:' . $baseDir);
        }

        $this->packageBaseDir = $packageBaseDir;
        $this->baseDir = $baseDir;

        // Load settings from this (if any)
        $this->initializeFromConfig();
    }

    /**
     * Load configuration from settings json file
     */
    protected function initializeFromConfig()
    {
        $configurationFile = $this->configurationFile();
        if (is_readable($configurationFile)) {
            $loadedConfiguration = json_decode(file_get_contents($configurationFile));
            $this->exchangeArray($loadedConfiguration);
        }
    }

    /**
     * File path
     *
     * @return string
     */
    protected function configurationFile()
    {
        $configurationFile = $this->getBaseDir()
            . DIRECTORY_SEPARATOR
            . '/'
            . $this->filename;
        return $configurationFile;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return $this->baseDir;
    }

    /**
     * @return string
     */
    public function getPackageBaseDir()
    {
        return $this->packageBaseDir;
    }

    /**
     * Destructor. Will write app config to file
     * to keep configuration persistent across app
     * runs.
     *
     * @todo throw a warning when file is not writable.
     */
    public function __destruct()
    {
        $configurationFile = $this->configurationFile();

        if (is_writable($this->getBaseDir()) &&
            (!file_exists($configurationFile) OR is_writable($configurationFile))
        ) {
            $jsonPretty = new JsonPretty();
            $json = $jsonPretty->prettify($this->getArrayCopy());
            file_put_contents($configurationFile, $json);
        }
    }
}