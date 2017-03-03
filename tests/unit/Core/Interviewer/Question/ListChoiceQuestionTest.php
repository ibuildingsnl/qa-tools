<?php

namespace Ibuildings\QaTools\UnitTest\Core\Interviewer\Question;

use Ibuildings\QaTools\Core\Exception\LogicException as LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\NoDefaultAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\ListChoiceQuestion;
use Ibuildings\QaTools\Test\MockeryTestCase;
use InvalidArgumentException;

/**
 * @group Conversation
 * @group Interviewer
 * @group Question
 */
class ListChoiceQuestionTest extends MockeryTestCase
{
    /**
     * @test
     *
     * @dataProvider \Ibuildings\QaTools\UnitTest\TestDataProvider::notString()
     */
    public function list_choice_questions_question_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        $multipleAnswers = new Choices([new TextualAnswer('An answer.')]);

        new ListChoiceQuestion($value, $multipleAnswers, $multipleAnswers);
    }

    /**
     * @test
     */
    public function list_choice_questions_choices_default_to_missing_answer_if_none_given()
    {
        $expectedDefaultAnswer = new NoDefaultAnswer;

        $question = new ListChoiceQuestion('A question?', new Choices([new TextualAnswer('An answer.')]));

        $this->assertEquals($expectedDefaultAnswer, $question->getDefaultAnswer());
    }

    /**
     * @test
     */
    public function list_choice_question_cannot_have_default_choices_that_are_not_possible_choices()
    {
        $this->expectException(LogicException::class);

        $possibleAnswers = new Choices([new TextualAnswer('An answer')]);
        $defaultAnswers  = new Choices([new TextualAnswer('A different answer')]);

        new ListChoiceQuestion('A question?', $possibleAnswers, $defaultAnswers);
    }

    /**
     * @test
     */
    public function list_choice_question_does_not_equal_another_list_choice_question_with_a_different_question()
    {
        $possibleAnswers = new Choices([new TextualAnswer('An answer.')]);
        $defaultAnswers  = new Choices([new TextualAnswer('An answer.')]);

        $listchoice          = new ListChoiceQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentlistchoice = new ListChoiceQuestion('Another question?', $possibleAnswers, $defaultAnswers);

        $this->assertFalse($listchoice->equals($differentlistchoice));
    }

    /**
     * @test
     */
    public function list_choice_question_does_not_equal_another_list_choice_question_with_different_default_choices()
    {
        $defaultAnswers           = new Choices([new TextualAnswer('An answer.')]);
        $possibleAnswers          = new Choices([new TextualAnswer('An answer.')]);
        $differentPossibleAnswers = new Choices(
            [
                new TextualAnswer('An answer.'),
                new TextualAnswer('Another answer.'),
            ]
        );

        $listchoice          = new ListChoiceQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentlistchoice = new ListChoiceQuestion('The question?', $differentPossibleAnswers, $defaultAnswers);

        $this->assertFalse($listchoice->equals($differentlistchoice));
    }

    /**
     * @test
     */
    public function list_choice_question_does_not_equal_another_list_choice_question_with_different_possible_choices()
    {
        $possibleAnswers         = new Choices(
            [
                new TextualAnswer('An answer.'),
                new TextualAnswer('Another answer.'),
            ]
        );
        $defaultAnswers          = new Choices([new TextualAnswer('An answer.')]);
        $differentDefaultAnswers = new Choices([new TextualAnswer('Another answer.')]);

        $listchoice          = new ListChoiceQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentlistchoice = new ListChoiceQuestion('The question?', $possibleAnswers, $differentDefaultAnswers);

        $this->assertFalse($listchoice->equals($differentlistchoice));
    }

    /**
     * @test
     */
    public function list_choice_question_equals_another_list_choice_question()
    {
        $choices = new Choices([new TextualAnswer('An answer.')]);

        $listchoice     = new ListChoiceQuestion('The question?', $choices, $choices);
        $samelistchoice = new ListChoiceQuestion('The question?', $choices, $choices);

        $this->assertTrue($listchoice->equals($samelistchoice));
    }

    /**
     * @test
     */
    public function list_choice_question_has_a_question_value()
    {
        $expectedQuestionValue = 'The question?';

        $choices             = new Choices([new TextualAnswer('An answer.')]);
        $listchoice           = new ListChoiceQuestion($expectedQuestionValue, $choices, $choices);
        $actualQuestionValue = $listchoice->getQuestion();

        $this->assertEquals($expectedQuestionValue, $actualQuestionValue);
    }

    /**
     * @test
     */
    public function list_choice_question_has_default_choices()
    {
        $expectedDefaultChoices = new Choices([new TextualAnswer('An answer.')]);

        $possibleChoices      = new Choices([new TextualAnswer('An answer.'), new TextualAnswer('Another answer')]);
        $question             = new ListChoiceQuestion('A question?', $possibleChoices, $expectedDefaultChoices);
        $actualDefaultChoices = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultChoices, $actualDefaultChoices);
    }

    /**
     * @test
     */
    public function list_choice_question_has_possible_choices()
    {
        $expectedPossibleAnswers = new Choices([new TextualAnswer('An answer.'), new TextualAnswer('Another answer')]);

        $defaultAnswers        = new Choices([new TextualAnswer('An answer.')]);
        $question              = new ListChoiceQuestion('A question?', $expectedPossibleAnswers, $defaultAnswers);
        $actualPossibleAnswers = $question->getPossibleChoices();

        $this->assertEquals($expectedPossibleAnswers, $actualPossibleAnswers);
    }

    /**
     * @test
     */
    public function list_choice_question_can_suggest_given_compatible_choices_that_are_possible_choices_as_default_answer()
    {
        $expectedDefaultChoices = new Choices([new TextualAnswer('Another answer')]);

        $question        = new ListChoiceQuestion(
            'A question?',
            new Choices([new TextualAnswer('An answer'), new TextualAnswer('Another answer')]),
            new Choices([new TextualAnswer('An answer')])
        );
        $updatedQuestion = $question->withDefaultAnswer($expectedDefaultChoices);

        $this->assertNotEquals($question, $updatedQuestion);
        $this->assertEquals($expectedDefaultChoices, $updatedQuestion->getDefaultAnswer());
    }

    /**
     * @test
     */
    public function list_choice_question_question_cannot_suggest_given_compatible_choices_that_are_not_possible_choices_as_default_answer()
    {
        $this->expectException(LogicException::class);

        $impossibleDefaultChoices = new Choices([new TextualAnswer('Not possible answer')]);

        $question = new ListChoiceQuestion(
            'A question?',
            new Choices([new TextualAnswer('An answer')]),
            new Choices([new TextualAnswer('An answer')])
        );
        $question->withDefaultAnswer($impossibleDefaultChoices);
    }

    /**
     * @test
     */
    public function list_choice_question_is_converted_to_string_correctly()
    {
        $question = 'A question?';
        $answer   = 'An answer';
        $expectedString = ListChoiceQuestion::class . '(question="' . $question . '", choices="' . $answer . '")';

        $actualQuestion = new ListChoiceQuestion($question, new Choices([new TextualAnswer($answer)]));

        $this->assertEquals($expectedString, (string) $actualQuestion);
    }
}
