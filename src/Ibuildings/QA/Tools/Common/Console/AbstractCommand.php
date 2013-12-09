<?php
/**
 * @author Matthijs van den Bos <matthijs@vandenbos.org>
 * @copyright 2013 Matthijs van den Bos
 */

namespace Ibuildings\QA\Tools\Common\Console;

use Ibuildings\QA\Tools\Common\CommandExistenceChecker;
use Ibuildings\QA\Tools\Common\Settings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AbstractCommand
 *
*@package Ibuildings\QA\Tools\Common\Console
 *
 * @SuppressWarnings(PHPMD)
 */
abstract class AbstractCommand extends Command
{
    /* Minimal version of git that is required, note that version stashing untracked files was not possible */
    const MINIMAL_VERSION_GIT = '1.7.8';

    /**
     * Lowest version of ant on which QA Tools is known to work
     * This could be increased to 1.8 since that makes it possible to use variables
     * instead of property names for if/unless constructs
     * see: https://ant.apache.org/manual/properties.html#if+unless
     */
    const MINIMAL_VERSION_ANT = '1.7.1';

    /**
     * @var Settings
     */
    protected $settings;

    /** @var DialogHelper */
    protected $dialog;

    /** @var \Twig_Environment */
    protected $twig;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->settings = new Settings();

        $this->dialog = $this->getHelperSet()->get('dialog');

        $loader = new \Twig_Loader_Filesystem(PACKAGE_BASE_DIR . '/config-dist');
        $this->twig = new \Twig_Environment($loader);
        $filter = new \Twig_SimpleFilter(
            'bool',
            function ($value) {
                if ($value) {
                    return 'true';
                } else {
                    return 'false';
                }
            }
        );
        $this->twig->addFilter($filter);

        $this->settings['dirname'] = basename(BASE_DIR);
    }
}
