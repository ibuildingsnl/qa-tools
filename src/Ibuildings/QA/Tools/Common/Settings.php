<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common;

use Camspiers\JsonPretty\JsonPretty;

class Settings extends \ArrayObject
{
    /**
     * @var string
     */
    protected $filename = 'qa-tools.json';

    /**
     * @var bool
     */
    protected $hasLoadedJsonFile = false;

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
            $loadedConfiguration = json_decode(file_get_contents($configurationFile), true);
            $this->exchangeArray($loadedConfiguration);
            $this->hasLoadedJsonFile = true;
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
     * Get the default value for a certain key/path. Nested arrays can be traversed by defining
     * the path as listing all the keys from top to bottom, separated by a dot ".". If the value
     * cannot be retrieved, the fallback value is returned.
     *
     * @param string $path
     * @param mixed  $fallback
     * @return mixed
     */
    public function getDefaultValueFor($path, $fallback)
    {
        $keys = explode('.', $path);

        $current = $this;
        while (!empty($keys)) {
            $key = reset($keys);
            if (!isset($current[$key])) {
                return $fallback;
            }

            if (count($keys) === 1) {
                return $current[$key];
            }

            $current = $current[$key];
            array_shift($keys);
        }
    }

    /**
     * @return bool
     */
    public function hasLoadedJsonFile()
    {
        return $this->hasLoadedJsonFile;
    }

    /**
     * @return bool
     */
    public function previousRunWasCompleted()
    {
        return isset($this['_qa_tools_run_completed']) && $this['_qa_tools_run_completed'];
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
            (!file_exists($configurationFile) or is_writable($configurationFile))
        ) {
            $jsonPretty = new JsonPretty();
            $json = $jsonPretty->prettify($this->getArrayCopy());
            file_put_contents($configurationFile, $json);
        }
    }
}
