<?php

use Ibuildings\QaTools\Core\IO\Cli\ConsoleQuestionFormatter;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\ListChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Conversation
 * @group Interviewer
 * @group Console
 * @group Question
 * @group Formatter
 */
class ConsoleQuestionFormatterTest extends TestCase
{
    /**
     * @test
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
     */
    public function formatter_formats_a_list_choice_question()
    {
        $questionFormatter = new ConsoleQuestionFormatter();

        $expectedFormattedQuestion = '<info>The question?</info> <comment>[The answer, Another answer]</comment>';
        $question                  = new ListChoiceQuestion(
            'The question?', new Choices([
                new TextualAnswer('The answer'),
                new TextualAnswer('Another answer'),
            ]),
            new Choices([
                new TextualAnswer('The answer'),
                new TextualAnswer('Another answer'),
            ])
        );

        $actualFormattedQuestion = $questionFormatter->formatListChoiceQuestion($question);

        $this->assertEquals($expectedFormattedQuestion, $actualFormattedQuestion);
    }
}
