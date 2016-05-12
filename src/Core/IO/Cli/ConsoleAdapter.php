<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Interviewer\ConversationHandler;
use Ibuildings\QaTools\Value\Answer\Factory\AnswerFactory;
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

    /**
     * @var ConsoleQuestionFactory
     */
    private $consoleQuestionFactory;


    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        FormatterHelper $formatterHelper,
        ConsoleQuestionFactory $consoleQuestionFactory
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->formatterHelper = $formatterHelper;
        $this->consoleQuestionFactory = $consoleQuestionFactory;
    }

    public function ask(Question $question)
    {
        $consoleQuestion = $this->consoleQuestionFactory->createFrom($question);

        $consoleAnswer = $this->questionHelper->ask($this->input, $this->output, $consoleQuestion);

        return AnswerFactory::createFrom($consoleAnswer);
    }
}
