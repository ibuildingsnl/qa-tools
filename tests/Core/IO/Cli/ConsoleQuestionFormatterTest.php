<?php

use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFormatter;
use Ibuildings\QaTools\Value\Answer\Choices;
use Ibuildings\QaTools\Value\Answer\TextualAnswer;
use Ibuildings\QaTools\Value\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Value\Question\ChecklistQuestion;
use Ibuildings\QaTools\Value\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Value\Question\TextualQuestion;
use Ibuildings\QaTools\Value\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class ConsoleQuestionFormatterTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group IO
     * @group Interviewer
     * @group Console
     */
    public function formatter_formats_a_yes_or_no_question_that_defaults_to_yes()
    {
        $questionFormatter = new ConsoleQuestionFormatter();
        
        $expectedFormattedQuestion = '<info>The question?</info> <comment>[Y/n]</comment>'.PHP_EOL.' > ';
        $question                  = new YesOrNoQuestion('The question?', YesOrNoAnswer::yes());

        $actualFormattedQuestion = $questionFormatter->formatYesOrNoQuestion($question);

        $this->assertEquals($expectedFormattedQuestion, $actualFormattedQuestion);
    }

    /**
     * @test
     * @group Conversation
     * @group IO
     * @group Interviewer
     * @group Console
     */
    public function formatter_formats_a_yes_or_no_question_that_defaults_to_no()
    {
        $questionFormatter = new ConsoleQuestionFormatter();

        $expectedFormattedQuestion = '<info>The question?</info> <comment>[y/N]</comment>'.PHP_EOL.' > ';
        $question                  = new YesOrNoQuestion('The question?', YesOrNoAnswer::no());

        $actualFormattedQuestion = $questionFormatter->formatYesOrNoQuestion($question);

        $this->assertEquals($expectedFormattedQuestion, $actualFormattedQuestion);
    }

    /**
     * @test
     * @group Conversation
     * @group IO
     * @group Interviewer
     * @group Console
     */
    public function formatter_formats_a_textual_question()
    {
        $questionFormatter = new ConsoleQuestionFormatter();

        $expectedFormattedQuestion = '<info>The question?</info> <comment>[The answer]</comment>'.PHP_EOL.' > ';
        $question                  = new TextualQuestion('The question?', new TextualAnswer('The answer'));

        $actualFormattedQuestion = $questionFormatter->formatTextualQuestion($question);

        $this->assertEquals($expectedFormattedQuestion, $actualFormattedQuestion);
    }

    /**
     * @test
     * @group Conversation
     * @group IO
     * @group Interviewer
     * @group Console
     */
    public function formatter_formats_a_multiple_choice_question()
    {
        $questionFormatter = new ConsoleQuestionFormatter();

        $expectedFormattedQuestion = '<info>The question?</info> <comment>[The answer]</comment>';
        $question                  = new MultipleChoiceQuestion(
            'The question?', new Choices([
                new TextualAnswer('The answer'),
                new TextualAnswer('Another answer'),
            ]),
            new TextualAnswer('The answer')
        );

        $actualFormattedQuestion = $questionFormatter->formatMultipleChoiceQuestion($question);

        $this->assertEquals($expectedFormattedQuestion, $actualFormattedQuestion);
    }

    /**
     * @test
     * @group Conversation
     * @group IO
     * @group Interviewer
     * @group Console
     */
    public function formatter_formats_a_checklist_question()
    {
        $questionFormatter = new ConsoleQuestionFormatter();

        $expectedFormattedQuestion = '<info>The question?</info> <comment>[The answer, Another answer]</comment>';
        $question                  = new ChecklistQuestion(
            'The question?', new Choices([
                new TextualAnswer('The answer'),
                new TextualAnswer('Another answer'),
            ]),
            new Choices([
                new TextualAnswer('The answer'),
                new TextualAnswer('Another answer'),
            ])
        );

        $actualFormattedQuestion = $questionFormatter->formatChecklistQuestion($question);

        $this->assertEquals($expectedFormattedQuestion, $actualFormattedQuestion);
    }
}
