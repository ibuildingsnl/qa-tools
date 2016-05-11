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
     * @var FormatterHelper
     */
    private $formatterHelper;

    public function __construct(QuestionHelper $questionHelper, FormatterHelper $formatterHelper)
    {
        $this->questionHelper = $questionHelper;
        $this->formatterHelper = $formatterHelper;
    }

    public function createWith(InputInterface $input, OutputInterface $output)
    {
        return new ConsoleAdapter($input, $output, $this->questionHelper, $this->formatterHelper);
    }
}
