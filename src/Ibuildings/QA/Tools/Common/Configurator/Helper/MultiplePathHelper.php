<?php
namespace Ibuildings\QA\Tools\Common\Configurator\Helper;

use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Helps to set multiple paths
 *
 * Class MultiplePathHelper
 *
 * @package Ibuildings\QA\Tools\Common\PHP\Configurator\Helper
 */
class MultiplePathHelper
{
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var DialogHelper
     */
    protected $dialog;
    /**
     * @var string
     */
    protected $baseDir;

    /**
     * @param OutputInterface $output
     * @param DialogHelper $dialog
     * @param string $baseDir
     */
    public function __construct(
        OutputInterface $output,
        DialogHelper $dialog,
        $baseDir
    ) {
        $this->output = $output;
        $this->dialog = $dialog;
        $this->baseDir = $baseDir;
    }

    /**
     * Asks the user for one or more patterns.
     *
     * @param string $pathQuestion
     * @param string $defaultPaths
     * @param null $confirmationQuestion Optional question to ask if you want to set the value
     * @param bool $defaultConfirmation
     *
     * @return string
     */
    public function askPatterns(
        $pathQuestion,
        $defaultPaths,
        $confirmationQuestion = null,
        $defaultConfirmation = true
    ) {
        $pathQuestion .= " [$defaultPaths]";

        $defaultConfirmationText = ' [Y/n] ';
        if ($defaultConfirmation === false) {
            $defaultConfirmationText = ' [y/N] ';
        }

        if ($confirmationQuestion) {
            if (!$this->dialog->askConfirmation(
                $this->output,
                $confirmationQuestion . $defaultConfirmationText,
                $defaultConfirmation
            )
            ) {
                return array();
            }
        }

        return $this->dialog->askAndValidate(
            $this->output,
            $pathQuestion,
            function ($data) {
                $paths = explode(',', $data);

                return $paths;
            },
            false,
            $defaultPaths
        );
    }

    /**
     * Asks the user for one or more paths, paths will be validated.
     *
     * @param string $pathQuestion
     * @param string $defaultPaths
     * @param null $confirmationQuestion Optional question to ask if you want to set the value
     * @param bool $defaultConfirmation
     *
     * @return string
     */
    public function askPaths(
        $pathQuestion,
        $defaultPaths,
        $confirmationQuestion = null,
        $defaultConfirmation = true
    ) {
        $pathQuestion .= " [$defaultPaths]";

        $defaultConfirmationText = ' [Y/n] ';
        if ($defaultConfirmation === false) {
            $defaultConfirmationText = ' [y/N] ';
        }

        if ($confirmationQuestion) {
            if (!$this->dialog->askConfirmation(
                $this->output,
                $confirmationQuestion . $defaultConfirmationText,
                $defaultConfirmation
            )
            ) {
                return array();
            }
        }

        return $this->dialog->askAndValidate(
            $this->output,
            $pathQuestion,
            function ($data) {
                $paths = explode(',', $data);
                foreach ($paths as $path) {
                    $fullPath = $this->baseDir . '/' . $path;
                    // Check paths
                    if (!is_dir($fullPath)) {
                        throw new \Exception("path '{$fullPath}' doesn't exist");
                    }
                }

                return $paths;
            },
            false,
            $defaultPaths
        );
    }
}
