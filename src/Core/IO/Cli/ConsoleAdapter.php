<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Interviewer\ConversationHandler;
use Ibuildings\QaTools\Core\Interviewer\Answer\Factory\AnswerFactory;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Ibuildings\QaTools\Core\Interviewer\Sentence;
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
     * @var ConsoleQuestionFactory
     */
    private $consoleQuestionFactory;


    public function __construct(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        ConsoleQuestionFactory $consoleQuestionFactory
    ) {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
        $this->consoleQuestionFactory = $consoleQuestionFactory;
    }

    public function ask(Question $question)
    {
        $consoleQuestion = $this->consoleQuestionFactory->createFrom($question);

        $consoleAnswer = $this->questionHelper->ask($this->input, $this->output, $consoleQuestion);

        return AnswerFactory::createFrom($consoleAnswer);
    }

    public function say(Sentence $sentence)
    {
        $this->output->writeln(sprintf('<info>%s</info>', $sentence->getSentence()));
    }

    public function error(Sentence $sentence)
    {
        $this->output->writeln(sprintf('<error>%s</error>', $sentence->getSentence()));
    }
}
