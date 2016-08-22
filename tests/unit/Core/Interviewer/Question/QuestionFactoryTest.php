<?php

namespace Ibuildings\QaTools\UnitTest\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Exception\InvalidArgumentException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\ListChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\QuestionFactory;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * @group Interviewer
 * @group Conversation
 * @group Question
 */
class QuestionFactoryTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_textual_question_with_something_other_than_non_empty_string_as_question_text($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        QuestionFactory::create($notNonEmptyString);
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

        QuestionFactory::create('QuestionFactory', $nonNullOrNotNonEmptyString);
    }

    /**
     * @test
     */
    public function question_creates_textual_question_without_default_answer()
    {
        $expectedQuestion = new TextualQuestion('A question?');

        $actualQuestion = QuestionFactory::create('A question?');

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     */
    public function question_creates_textual_question_with_default_answer()
    {
        $expectedQuestion = new TextualQuestion('A question?', new TextualAnswer('An answer'));

        $actualQuestion = QuestionFactory::create('A question?', 'An answer');

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

        QuestionFactory::createYesOrNo($notNonEmptyString);
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

        QuestionFactory::createYesOrNo('A question?', $notNullOrBoolean);
    }

    /**
     * @test
     */
    public function question_creates_yes_or_no_question_without_default_answer()
    {
        $expectedQuestion = new YesOrNoQuestion('A question?');

        $actualQuestion = QuestionFactory::createYesOrNo('A question?');

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     */
    public function question_creates_yes_or_no_question_with_default_answer_yes()
    {
        $expectedQuestion = new YesOrNoQuestion('A question?', YesOrNoAnswer::yes());

        $actualQuestion = QuestionFactory::createYesOrNo('A question?', YesOrNoAnswer::YES);

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }

    /**
     * @test
     */
    public function question_creates_yes_or_no_question_with_default_answer_no()
    {
        $expectedQuestion = new YesOrNoQuestion('A question?', YesOrNoAnswer::no());

        $actualQuestion = QuestionFactory::createYesOrNo('A question?', YesOrNoAnswer::NO);

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

        QuestionFactory::createMultipleChoice($notNonEmptyString, ['An answer']);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_multiple_choice_question_with_something_other_than_non_empty_strings_as_choices($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        QuestionFactory::createMultipleChoice('A question', [$notNonEmptyString]);
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

        QuestionFactory::createMultipleChoice('QuestionFactory', ['An answer'], $nonNullOrNotNonEmptyString);
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

        $actualQuestion = QuestionFactory::createMultipleChoice('A question', ['An answer', 'Another answer']);

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

        $actualQuestion = QuestionFactory::createMultipleChoice('A question', ['An answer', 'Another answer'], 'An answer');

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

        QuestionFactory::createListChoice($notNonEmptyString, ['An answer']);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_list_choice_question_with_something_other_than_non_empty_strings_as_choices($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        QuestionFactory::createListChoice('A question', [$notNonEmptyString]);
    }

    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notStringOrEmptyString()
     */
    public function question_cannot_create_list_choice_question_with_something_other_than_non_empty_strings_as_default_answer($notNonEmptyString)
    {
        $this->expectException(InvalidArgumentException::class);

        QuestionFactory::createListChoice('A question', ['An answer'], [$notNonEmptyString]);
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

        $actualQuestion = QuestionFactory::createListChoice('A question', ['An answer', 'Another answer'], ['An answer']);

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

        $actualQuestion = QuestionFactory::createListChoice('A question', ['An answer', 'Another answer']);

        $this->assertEquals($expectedQuestion, $actualQuestion);
    }
}
