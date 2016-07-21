<?php

namespace Ibuildings\QaTools\UnitTest\Core\Interviewer;

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question;
use Ibuildings\QaTools\Core\Interviewer\Question\ListChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Interviewer
 * @group Conversation
 * @group Question
 */
class QuestionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_textual_question_with_something_other_than_non_empty_string_as_question_text($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        Question::create($notNonEmptyString);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_textual_question_with_something_other_than_a_null_or_non_empty_string_as_default_answer($nonNullOrNotNonEmptyString)
    {
        if ($nonNullOrNotNonEmptyString === null) {
            return;
        }

        $this->expectException(InvalidArgumentException::class);

        Question::create('Question', $nonNullOrNotNonEmptyString);
    }


    
    /**
     * @test
     */
    public function question_creates_textual_question_without_default_answer()
    {
        $expectedQuestion = new TextualQuestion('A question?');

        $actualQuestion = Question::create('A question?');

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }
    
    /**
     * @test
     */
    public function question_creates_textual_question_with_default_answer()
    {
        $expectedQuestion = new TextualQuestion('A question?', new TextualAnswer('An answer'));

        $actualQuestion = Question::create('A question?', 'An answer');

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_yes_or_no_question_with_something_other_than_non_empty_string_as_question_text($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        Question::createYesOrNo($notNonEmptyString);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notBoolean()
     */
    public function question_cannot_create_yes_or_no_question_with_something_other_than_null_or_boolean_as_default_answer($notNullOrBoolean)
    {
        if ($notNullOrBoolean === null) {
            return;
        }

        $this->expectException(InvalidArgumentException::class);

        Question::createYesOrNo('A question?', $notNullOrBoolean);
    }

    /**
     * @test
     */
    public function question_creates_yes_or_no_question_without_default_answer()
    {
        $expectedQuestion = new YesOrNoQuestion('A question?');

        $actualQuestion = Question::createYesOrNo('A question?');

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }
    
    /**
     * @test
     */
    public function question_creates_yes_or_no_question_with_default_answer_yes()
    {
        $expectedQuestion = new YesOrNoQuestion('A question?', YesOrNoAnswer::yes());

        $actualQuestion = Question::createYesOrNo('A question?', YesOrNoAnswer::YES);

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     */
    public function question_creates_yes_or_no_question_with_default_answer_no()
    {
        $expectedQuestion = new YesOrNoQuestion('A question?', YesOrNoAnswer::no());

        $actualQuestion = Question::createYesOrNo('A question?', YesOrNoAnswer::NO);

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_multiple_choice_question_with_something_other_than_non_empty_string_as_question_text($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        Question::createMultipleChoice($notNonEmptyString, ['An answer']);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_multiple_choice_question_with_something_other_than_non_empty_strings_as_choices($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        Question::createMultipleChoice('A question', [$notNonEmptyString]);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_multiple_choice_question_with_something_other_than_a_null_or_non_empty_string_as_default_answer($nonNullOrNotNonEmptyString)
    {
        if ($nonNullOrNotNonEmptyString === null) {
            return;
        }

        $this->expectException(InvalidArgumentException::class);

        Question::createMultipleChoice('Question', ['An answer'], $nonNullOrNotNonEmptyString);
    }


    /**
     * @test
     */
    public function question_creates_multiple_choice_question_without_default_answer()
    {
        $expectedQuestion = new MultipleChoiceQuestion(
            'A question',
            new Choices([
                new TextualAnswer('An answer'),
                new TextualAnswer('Another answer'),
            ])
        );

        $actualQuestion = Question::createMultipleChoice('A question', ['An answer', 'Another answer']);

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     */
    public function question_creates_multiple_choice_question_with_default_answer()
    {
        $expectedQuestion = new MultipleChoiceQuestion(
            'A question',
            new Choices([
                new TextualAnswer('An answer'),
                new TextualAnswer('Another answer'),
            ]),
            new TextualAnswer('An answer')
        );

        $actualQuestion = Question::createMultipleChoice('A question', ['An answer', 'Another answer'], 'An answer');

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_list_choice_question_with_something_other_than_non_empty_string_as_question_text($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        Question::createListChoice($notNonEmptyString, ['An answer']);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_list_choice_question_with_something_other_than_non_empty_strings_as_choices($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        Question::createListChoice('A question', [$notNonEmptyString]);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_list_choice_question_with_something_other_than_non_empty_strings_as_default_answer($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        Question::createListChoice('A question', ['An answer'], [$notNonEmptyString]);
    }

    /**
     * @test
     */
    public function question_creates_list_choice_question_without_default_answer()
    {
        $expectedQuestion = new ListChoiceQuestion(
            'A question',
            new Choices([
                new TextualAnswer('An answer'),
                new TextualAnswer('Another answer'),
            ]),
            new Choices([
                new TextualAnswer('An answer')
            ])
        );

        $actualQuestion = Question::createListChoice('A question', ['An answer', 'Another answer'], ['An answer']);

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }
    
    /**
     * @test
     */
    public function question_creates_list_choice_question_with_default_answer()
    {
        $expectedQuestion = new ListChoiceQuestion(
            'A question',
            new Choices([
                new TextualAnswer('An answer'),
                new TextualAnswer('Another answer'),
            ])
        );

        $actualQuestion = Question::createListChoice('A question', ['An answer', 'Another answer']);

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }
}
