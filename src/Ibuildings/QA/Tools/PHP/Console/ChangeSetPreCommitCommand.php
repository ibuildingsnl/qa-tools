<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\PHP\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ChangeSetPreCommitCommand
 * @package Ibuildings\QA\Tools\PHP\Console
 *
 * @SuppressWarnings(PHPMD)
 */
class ChangeSetPreCommitCommand extends Command
{
    protected $settings = array();

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

        $gitCommand = 'git diff --cached --diff-filter=ACM --name-only ' . implode(' ', $paths);

        $changeset =  `$gitCommand`;

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
}
