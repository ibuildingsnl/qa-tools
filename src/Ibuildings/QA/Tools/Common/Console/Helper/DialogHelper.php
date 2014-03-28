<?php
namespace Ibuildings\QA\Tools\Common\Console\Helper;

use Symfony\Component\Console\Helper\DialogHelper as BaseDialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DialogHelper
 *
 * @package Ibuildings\QA\Tools\Common\Console\Helper
 */
class DialogHelper extends BaseDialogHelper
{
    /**
     * Asks a confirmation to the user.
     *
     * The question will be asked until the user answers by nothing, yes, or no.
     *
     * @param OutputInterface $output   An Output instance
     * @param string|array    $question The question to ask
     * @param Boolean         $default  The default answer if the user enters nothing
     *
     * @return Boolean true if the user has confirmed, false otherwise
     */
    public function askConfirmation(OutputInterface $output, $question, $default = true)
    {
        $hint = ($default) ? '[Y/n] ' : '[y/N] ';
        $question = $question . ' ' . $hint;

        return parent::askConfirmation($output, $question, $default);
    }
}