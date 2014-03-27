<?php

/**
 * This file is part of Ibuildings QA-Tools.
 *
 * (c) Ibuildings
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ChangeSetPreCommitCommand
 *
 * @package Ibuildings\QA\Tools\Common\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class ChangeSetPreCommitCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('changeset:pre-commit')
            ->setDescription('Returns a string containing the changeset for this commit')
            ->setHelp(
                "Returns a string containing the changeset for this commit. Default is the entire changeset, separated by newlines"
            )
            ->addOption(
                'filter-path',
                'fp',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'The path to filter the changeset by. This option can be set multiple times'
            )
            ->addOption(
                'filter-ext',
                'fe',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED,
                'The extension to filter the changeset by. This option can be set multiple times'
            )
            ->addOption(
                'separator',
                's',
                InputOption::VALUE_REQUIRED,
                'The separator to separate individual changeset items with'
            );

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $paths = $input->getOption('filter-path');
        $exts = $input->getOption('filter-ext');
        $sep = $input->getOption('separator');

        $changeset = $this->getChangeSet($paths);

        // only do further processing if we have extension filter options or a separator option
        if (count($exts) || null !== $sep) {
            $items = explode("\n", $changeset);

            foreach ($items as $key => $item) {
                if (!in_array(pathinfo($item, PATHINFO_EXTENSION), $exts) || empty($item)) {
                    unset($items[$key]);
                }
            }

            if (null !== $sep) {
                $changeset = implode($sep, $items);
            } else {
                $changeset = implode("\n", $items);
            }
        }

        echo $changeset;
    }

    /**
     * Checks the given paths for changed files and return those that are found
     *
     * @param string $paths
     *
     * @return array
     *
     * ignore code as this is purely commandline and we don't want to test that
     * @codeCoverageIgnore
     */
    protected function getChangeSet($paths)
    {
        $gitCommand = 'git diff --cached --diff-filter=ACM --name-only ' . implode(' ', $paths);

        $changeSet = `$gitCommand`;

        return $changeSet;
    }
}
