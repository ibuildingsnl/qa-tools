<?php

namespace Ibuildings\QaTools\Core\IO\Cli;

use Ibuildings\QaTools\Core\Interviewer\MemorizingInterviewer;
use Ibuildings\QaTools\Core\IO\File\FileHandler;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InterviewerFactory
{
    /**
     * @var FileHandler
     */
    private $fileHandler;

    /**
     * @var QuestionHelper
     */
    private $questionHelper;

    /**
     * @var ConsoleQuestionFactory
     */
    private $consoleQuestionFactory;

    public function __construct(
        FileHandler $fileHandler,
        QuestionHelper $questionHelper,
        ConsoleQuestionFactory $consoleQuestionFactory
    ) {
        $this->fileHandler = $fileHandler;
        $this->questionHelper = $questionHelper;
        $this->consoleQuestionFactory = $consoleQuestionFactory;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Interviewer
     */
    public function createWith(InputInterface $input, OutputInterface $output)
    {
        return new Interviewer($input, $output, $this->questionHelper, $this->consoleQuestionFactory);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param array $previousAnswers
     * @return MemorizingInterviewer
     */
    public function createMemorizingWith(InputInterface $input, OutputInterface $output, array $previousAnswers)
    {
        return new MemorizingInterviewer($this->createWith($input, $output), $previousAnswers);
    }
}
