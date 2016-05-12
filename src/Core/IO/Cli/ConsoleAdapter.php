<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Interviewer\ConversationHandler;
use Ibuildings\QaTools\Value\Question\Question;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsoleAdapter implements ConversationHandler
{
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var FormatterHelper
     */
    private $formatterHelper;


    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        FormatterHelper $formatterHelper
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->formatterHelper = $formatterHelper;
    }

    public function ask(Question $question)
    {
        $consoleQuestion = ConsoleQuestionFactory::createFrom($question);

        $consoleAnswer = $this->questionHelper->ask($this->input, $this->output, $consoleQuestion);

        return ConsoleAnswerMapper::mapToQaToolsAnswer($consoleAnswer);
    }
}
