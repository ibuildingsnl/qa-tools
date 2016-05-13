<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleAdapterFactory
{
    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var ConsoleQuestionFactory
     */
    private $consoleQuestionFactory;

    public function __construct(
        QuestionHelper $questionHelper,
        ConsoleQuestionFactory $consoleQuestionFactory
    ) {
        $this->questionHelper           = $questionHelper;
        $this->consoleQuestionFactory   = $consoleQuestionFactory;
    }

    public function createWith(InputInterface $input, OutputInterface $output)
    {
        return new ConsoleAdapter(
            $input,
            $output,
            $this->questionHelper,
            $this->consoleQuestionFactory
        );
    }
}
