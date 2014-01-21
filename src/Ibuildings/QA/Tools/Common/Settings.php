<?php

namespace Ibuildings\QA\Tools\Common;

class Settings extends \ArrayObject
{
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

        $this['packageBaseDir'] = $packageBaseDir;
        $this['baseDir'] = $baseDir;
    }

    /**
     * @return string
     */
    public function getBaseDir()
    {
        return $this['baseDir'];
    }

    /**
     * @return string
     */
    public function getPackageBaseDir()
    {
        return $this['packageBaseDir'];
    }
}