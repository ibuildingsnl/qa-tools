<?php

namespace Ibuildings\QA\Tools\Common;

class Settings extends \ArrayObject
{
    /**
     * Will setup some global Application Configuration and is used to retain
     * Application state between runs.
     */
    public function __construct()
    {
    }

    /**
     * Manually call the initialize() to setup some base app config settings
     *
     * @throws \Exception
     */
    public function initialize()
    {
        $this['packageBaseDir'] = realpath(__DIR__ . '/../../../../../');
        if (!is_dir($this['packageBaseDir'])) {
            throw new \Exception('Cannot find vendor package dir:' . $this['packageBaseDir']);
        }

        $this['baseDir'] = realpath($this['packageBaseDir'] . '/../../../');
        if (!is_dir($this['baseDir'])) {
            throw new \Exception('Cannot find project base dir:' . $this['baseDir']);
        }
    }
}