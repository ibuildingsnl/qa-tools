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

use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class InstallJsHintCommand
 *
 * @package Ibuildings\QA\tests\mock
 */
class InstallJsHintCommand extends \Ibuildings\QA\Tools\Javascript\Console\InstallJsHintCommand
{
    /**
     * @var CommandExistenceChecker
     */
    protected $checker;

    /**
     * @param \Ibuildings\QA\Tools\Common\CommandExistenceChecker $checker
     */
    public function setChecker($checker)
    {
        $this->checker = $checker;
    }

    /**
     * Overwrite to be able to use a mock commitExistence checker
     *
     * @return CommandExistenceChecker
     */
    protected function getCommitExistenceChecker()
    {
        if (isset($this->checker)) {
            return $this->checker;
        }

        return new CommandExistenceChecker();
    }
}
