<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\tests\mock;

use Ibuildings\QA\Tools\Common\Settings;

/**
 * Class Registry
 *
 * This mock class is intended to catch certain special configurations and create mock classes for them so we can
 * make sure we don't write to the file system for those configurations
 *
 * @package Ibuildings\QA\tests\mock
 */
class SettingsMock extends Settings
{
    public function __destruct()
    {
        // remove qa-tools.json if written
        if (file_exists($this->configurationFile())) {
            unlink($this->configurationFile());
        }
    }
}
