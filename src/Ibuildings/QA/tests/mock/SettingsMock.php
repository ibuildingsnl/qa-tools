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

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Registry
 *
 * This mock class is intented to catch certain special configurations and create mock classes for them so we can
 * make sure we don;t write to the file system for those configurations
 *
 * @package Ibuildings\QA\tests\mock
 */
class SettingsMock extends \Ibuildings\QA\Tools\Common\Settings
{
    public function __destruct()
    {
        //do nothing for testing
    }
}
