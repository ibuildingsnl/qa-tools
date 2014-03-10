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
 * Class RunCommand
 *
 * @package Ibuildings\QA\tests\mock
 */
class RunCommand extends \Ibuildings\QA\Tools\Common\Console\RunCommand
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
     * Overwrite to be able to use a mock commandExistence checker
     *
     * @return CommandExistenceChecker
     */
    protected function getCommandExistenceChecker()
    {
        if (isset($this->checker)) {
            return $this->checker;
        }

        return new CommandExistenceChecker();
    }

    /**
     * We don't want to really run ant so overwrite the function
     *
     * @param string $verbose
     * @param string $target
     * @param string $dirOption
     *
     * @return int|void
     */
    protected function runAnt($verbose, $target, $dirOption)
    {
        return'don\'t exit php unit would not like that';
    }
}
