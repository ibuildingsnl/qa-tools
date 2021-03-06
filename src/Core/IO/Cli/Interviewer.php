<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Answer\AnswerFactory;
use Ibuildings\QaTools\Core\Interviewer\Interviewer as InterviewerInterface;
use Ibuildings\QaTools\Core\Interviewer\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Interviewer implements InterviewerInterface
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

    public function notice($sentence)
    {
        Assertion::string($sentence, 'Expected sentence to be a string, got "%s" of type "%s"', 'sentence');

        $this->output->writeln(sprintf('<comment>%s</comment>', $sentence));
    }

    public function giveDetails($sentence)
    {
        Assertion::string($sentence, 'Expected sentence to be a string, got "%s" of type "%s"', 'sentence');

        $this->output->writeln(sprintf('%s', $sentence));
    }

    public function success($sentence)
    {
        Assertion::string($sentence, 'Expected sentence to be a string, got "%s" of type "%s"', 'sentence');

        $this->output->writeln(sprintf('<fg=green>%s</>', $sentence));
    }

    public function warn($sentence)
    {
        Assertion::string($sentence, 'Expected sentence to be a string, got "%s" of type "%s"', 'sentence');

        $this->output->writeln(sprintf('<error>%s</error>', $sentence));
    }
}
