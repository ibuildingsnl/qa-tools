<?php

namespace Ibuildings\QaTools\UnitTest\Core\IO\Cli;

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFactory;
use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFormatter;
use Ibuildings\QaTools\Core\IO\Cli\Interviewer;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @group Conversation
 * @group Interviewer
 */
class InterviewerTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function interviewer_can_only_say_sentences_that_are_strings($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        $interviewer = new Interviewer(
            new ArgvInput,
            new DummyOutput,
            new QuestionHelper,
            new ConsoleQuestionFactory(new ConsoleQuestionFormatter)
        );

        $interviewer->say($notNonEmptyString);
    }

    /**
     * @test
     *
     * @dataProvider Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString
     */
    public function interviewer_can_only_warn_with_sentences_that_are_strings($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        $interviewer = new Interviewer(
            new ArgvInput,
            new DummyOutput,
            new QuestionHelper,
            new ConsoleQuestionFactory(new ConsoleQuestionFormatter)
        );

        $interviewer->warn($notNonEmptyString);
    }
}
