<?php

use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\MissingAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class MultipleChoiceQuestionTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function multiple_choice_questions_question_can_only_be_string($value)
    {
        $this->expectException(\Ibuildings\QaTools\Core\Exception\InvalidArgumentException::class);

        $defaultAnswer   = new TextualAnswer('An answer.');
        $multipleAnswers = new Choices([new TextualAnswer('An answer.')]);

        new MultipleChoiceQuestion($value, $multipleAnswers, $defaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_questions_answer_defaults_to_missing_answer_if_none_given()
    {
        $expectedDefaultAnswer = new MissingAnswer;

        $question = new MultipleChoiceQuestion('A question?', new Choices([new TextualAnswer('An answer.')]));

        $this->assertEquals($expectedDefaultAnswer, $question->getDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_cannot_have_a_default_answer_that_is_not_a_possible_choice()
    {
        $this->expectException(LogicException::class);

        $possibleChoices = new Choices([new TextualAnswer('An answer')]);
        $defaultAnswer   = new TextualAnswer('A different answer');

        new MultipleChoiceQuestion('A question?', $possibleChoices, $defaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_does_not_equal_another_multiple_choice_question_with_a_different_question()
    {
        $possibleChoices = new Choices([new TextualAnswer('An answer.')]);
        $defaultAnswer   = new TextualAnswer('An answer.');

        $question          = new MultipleChoiceQuestion('The question?', $possibleChoices, $defaultAnswer);
        $differentQuestion = new MultipleChoiceQuestion('Another question?', $possibleChoices, $defaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_does_not_equal_another_multiple_choice_question_with_a_different_default_answer(
    )
    {
        $possibleChoices = new Choices(
            [new TextualAnswer('An answer.'), new TextualAnswer('A different answer.')]
        );

        $defaultAnswer          = new TextualAnswer('An answer.');
        $differentDefaultAnswer = new TextualAnswer('A different answer.');

        $question          = new MultipleChoiceQuestion('The question?', $possibleChoices, $defaultAnswer);
        $differentQuestion = new MultipleChoiceQuestion('The question?', $possibleChoices, $differentDefaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_does_not_equal_another_multiple_choice_question_with_different_possible_choices(
    )
    {
        $possibleChoices          = new Choices([new TextualAnswer('An answer.')]);
        $differentPossibleChoices = new Choices(
            [
                new TextualAnswer('An answer.'),
                new TextualAnswer('A different answer.'),
            ]
        );

        $defaultAnswer = new TextualAnswer('An answer.');

        $question          = new MultipleChoiceQuestion('The question?', $possibleChoices, $defaultAnswer);
        $differentQuestion = new MultipleChoiceQuestion('The question?', $differentPossibleChoices, $defaultAnswer);

        $this->assertFalse($question->equals($differentQuestion));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_equals_another_multiple_choice_question()
    {
        $possibleChoices = new Choices([new TextualAnswer('An answer.')]);
        $defaultAnswer   = new TextualAnswer('An answer.');

        $question = new MultipleChoiceQuestion('The question?', $possibleChoices, $defaultAnswer);
        $same     = new MultipleChoiceQuestion('The question?', $possibleChoices, $defaultAnswer);

        $this->assertTrue($question->equals($same));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_has_a_question_value()
    {
        $expectedQuestionValue = 'The question?';

        $possibleChoices     = new Choices([new TextualAnswer('An answer.')]);
        $question            = new MultipleChoiceQuestion(
            $expectedQuestionValue,
            $possibleChoices,
            new TextualAnswer('An answer.')
        );
        $actualQuestionValue = $question->getQuestion();

        $this->assertEquals($expectedQuestionValue, $actualQuestionValue);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_has_a_default_answer()
    {
        $expectedDefaultAnswer = new TextualAnswer('An answer.');

        $possibleChoices     = new Choices(
            [
                new TextualAnswer('An answer.'),
                new TextualAnswer('Another answer'),
            ]
        );
        $question            = new MultipleChoiceQuestion('A question?', $possibleChoices, $expectedDefaultAnswer);
        $actualDefaultAnswer = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultAnswer, $actualDefaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_has_possible_answers()
    {
        $expectedPossibleChoices = new Choices(
            [new TextualAnswer('An answer.'), new TextualAnswer('Another answer')]
        );

        $defaultAnswer         = new TextualAnswer('An answer.');
        $question              = new MultipleChoiceQuestion('A question?', $expectedPossibleChoices, $defaultAnswer);
        $actualPossibleChoices = $question->getPossibleChoices();

        $this->assertEquals($expectedPossibleChoices, $actualPossibleChoices);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_can_suggest_a_given_compatible_answer_that_is_a_possible_choice_as_default_answer()
    {
        $expectedDefaultAnswer = new TextualAnswer('Another answer');

        $question        = new MultipleChoiceQuestion(
            'A question?',
            new Choices([new TextualAnswer('An answer'), new TextualAnswer('Another answer')]),
            new TextualAnswer('An answer')
        );
        $updatedQuestion = $question->withDefaultAnswer($expectedDefaultAnswer);

        $this->assertNotEquals($question, $updatedQuestion);
        $this->assertEquals($expectedDefaultAnswer, $updatedQuestion->getDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_cannot_suggest_a_given_compatible_answer_that_is_not_possible_as_default_answer()
    {
        $this->expectException(LogicException::class);

        $impossibleDefaultAnswer = new TextualAnswer('Not possible answer');

        $question = new MultipleChoiceQuestion(
            'A question?',
            new Choices([new TextualAnswer('An answer')]),
            new TextualAnswer('An answer')
        );
        $question->withDefaultAnswer($impossibleDefaultAnswer);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_with_default_answer_can_calculate_its_hash()
    {
        $expectedHash = 'bb24e0cb20d9689e6b510449d6d9dd20';
        $question     = new MultipleChoiceQuestion(
            'A question?',
            new Choices([new TextualAnswer('An answer.')]),
            new TextualAnswer('An answer.')
        );

        $this->assertEquals($expectedHash, $question->calculateHash());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function multiple_choice_question_without_default_answer_can_calculate_its_hash()
    {
        $expectedHash = '15a10341eeee07ee726ab550f0437541';
        $question     = new MultipleChoiceQuestion(
            'A question?',
            new Choices([new TextualAnswer('An answer.')])
        );

        $this->assertEquals($expectedHash, $question->calculateHash());
    }
}
