<?php

use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFactory;
use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFormatter;
use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\ChecklistQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\Question as QaToolsQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class ConsoleQuestionFactoryTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Factory
     * @group Interviewer
     * @group Console
     */
    public function factory_throws_exception_when_a_non_supported_question_type_is_given()
    {
        $formatterDummy = Mockery::mock(ConsoleQuestionFormatter::class);
        $questionDummy  = Mockery::mock(QaToolsQuestion::class);

        $this->expectException(InvalidArgumentException::class);

        $factory = new ConsoleQuestionFactory($formatterDummy);
        $factory->createFrom($questionDummy);
    }

    /**
     * @test
     * @group Conversation
     * @group Factory
     * @group Interviewer
     * @group Console
     */
    public function factory_creates_a_console_question_from_positive_yes_or_no_question()
    {
        $question      = 'The question?';
        $defaultAnswer = YesOrNoAnswer::yes();

        $formatterMock = Mockery::mock(ConsoleQuestionFormatter::class);
        $formatterMock
            ->shouldReceive('formatYesOrNoQuestion')
            ->andReturn($question);

        $factory               = new ConsoleQuestionFactory($formatterMock);
        $actualConsoleQuestion = $factory->createFrom(new YesOrNoQuestion($question, $defaultAnswer));

        $this->assertInstanceOf(Question::class, $actualConsoleQuestion);
        $this->assertEquals($question, $actualConsoleQuestion->getQuestion());
    }

    /**
     * @test
     * @group Conversation
     * @group Factory
     * @group Interviewer
     * @group Console
     */
    public function factory_creates_a_console_question_from_negative_yes_or_no_question()
    {
        $question      = 'The question?';
        $defaultAnswer = YesOrNoAnswer::no();

        $formatterMock = Mockery::mock(ConsoleQuestionFormatter::class);
        $formatterMock
            ->shouldReceive('formatYesOrNoQuestion')
            ->andReturn($question);

        $factory               = new ConsoleQuestionFactory($formatterMock);
        $actualConsoleQuestion = $factory->createFrom(new YesOrNoQuestion($question, $defaultAnswer));

        $this->assertInstanceOf(Question::class, $actualConsoleQuestion);
        $this->assertEquals($question, $actualConsoleQuestion->getQuestion());
    }

    /**
     * @test
     * @group Conversation
     * @group Factory
     * @group Interviewer
     * @group Console
     */
    public function factory_creates_a_console_question_from_textual_question()
    {
        $question      = 'The question?';
        $defaultAnswer = new TextualAnswer('The answer');

        $formatterMock = Mockery::mock(ConsoleQuestionFormatter::class);
        $formatterMock
            ->shouldReceive('formatTextualQuestion')
            ->andReturn($question);

        $factory               = new ConsoleQuestionFactory($formatterMock);
        $actualConsoleQuestion = $factory->createFrom(new TextualQuestion($question, $defaultAnswer));

        $this->assertInstanceOf(Question::class, $actualConsoleQuestion);
        $this->assertEquals($question, $actualConsoleQuestion->getQuestion());
    }

    /**
     * @test
     * @group Conversation
     * @group Factory
     * @group Interviewer
     * @group Console
     */
    public function factory_creates_a_console_question_from_multiple_choice_question()
    {
        $question        = 'The question?';
        $answerText      = 'The answer';
        $possibleChoices = new Choices([new TextualAnswer($answerText)]);
        $defaultAnswer   = new TextualAnswer('The answer');

        $expectedConsoleQuestion = new ChoiceQuestion($question, [$answerText], $defaultAnswer->getAnswer());
        $expectedConsoleQuestion->setMaxAttempts(ConsoleQuestionFactory::MAX_ATTEMPTS);

        $formatterMock = Mockery::mock(ConsoleQuestionFormatter::class);
        $formatterMock
            ->shouldReceive('formatMultipleChoiceQuestion')
            ->andReturn($question);

        $factory               = new ConsoleQuestionFactory($formatterMock);
        $actualConsoleQuestion = $factory->createFrom(
            new MultipleChoiceQuestion($question, $possibleChoices, $defaultAnswer)
        );

        $this->assertInstanceOf(Question::class, $actualConsoleQuestion);
        $this->assertEquals($question, $actualConsoleQuestion->getQuestion());
    }

    /**
     * @test
     * @group Conversation
     * @group Factory
     * @group Interviewer
     * @group Console
     */
    public function factory_creates_a_console_question_from_checklist_question()
    {
        $question        = 'The question?';
        $answerText      = 'The answer';
        $possibleChoices = new Choices([new TextualAnswer($answerText)]);
        $defaultChoices  = new Choices([new TextualAnswer($answerText)]);

        $expectedConsoleQuestion = new ChoiceQuestion($question, [$answerText], $answerText);
        $expectedConsoleQuestion->setMultiselect(true);
        $expectedConsoleQuestion->setMaxAttempts(ConsoleQuestionFactory::MAX_ATTEMPTS);

        $formatterMock = Mockery::mock(ConsoleQuestionFormatter::class);
        $formatterMock
            ->shouldReceive('formatChecklistQuestion')
            ->andReturn($question);

        $factory               = new ConsoleQuestionFactory($formatterMock);
        $actualConsoleQuestion = $factory->createFrom(
            new ChecklistQuestion($question, $possibleChoices, $defaultChoices)
        );

        $this->assertEquals($expectedConsoleQuestion, $actualConsoleQuestion);
    }
}
