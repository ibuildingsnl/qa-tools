<?php

namespace Ibuildings\QaTools\UnitTest\Core\IO\Cli;

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFactory;
use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFormatter;
use Ibuildings\QaTools\Core\IO\Cli\Interviewer;
use Ibuildings\QaTools\Test\MockeryTestCase;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tests\Fixtures\DummyOutput;

/**
 * @group Conversation
 * @group Interviewer
 */
class InterviewerTest extends MockeryTestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notString
     */
    public function interviewer_can_only_say_sentences_that_are_strings($nonString)
    {
        $this->expectException(InvalidArgumentException::class);

        $interviewer = new Interviewer(
            new ArgvInput,
            new DummyOutput,
            new QuestionHelper,
            new ConsoleQuestionFactory(new ConsoleQuestionFormatter)
        );

        $interviewer->notice($nonString);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notString
     */
    public function interviewer_can_only_warn_with_sentences_that_are_strings($nonString)
    {
        $this->expectException(InvalidArgumentException::class);

        $interviewer = new Interviewer(
            new ArgvInput,
            new DummyOutput,
            new QuestionHelper,
            new ConsoleQuestionFactory(new ConsoleQuestionFormatter)
        );

        $interviewer->warn($nonString);
    }
}
