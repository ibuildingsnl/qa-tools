<?php

use Ibuildings\QaTools\Exception\LogicException as LogicException;
use Ibuildings\QaTools\Value\Answer\MultipleAnswers;
use Ibuildings\QaTools\Value\Answer\SingleAnswer;
use Ibuildings\QaTools\Value\Question\ChecklistQuestion;
use Ibuildings\QaTools\Value\Question\OpenQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class ChecklistQuestionTest extends TestCase
{
    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function question_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        $multipleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);

        new ChecklistQuestion($value, $multipleAnswers, $multipleAnswers);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_cannot_have_a_default_value_that_is_not_a_possible_answer()
    {
        $this->expectException(LogicException::class);

        $possibleAnswers = new MultipleAnswers([new SingleAnswer('An answer')]);
        $defaultAnswers  = new MultipleAnswers([new SingleAnswer('A different answer')]);

        new ChecklistQuestion('A question?', $possibleAnswers, $defaultAnswers);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_a_different_question()
    {
        $possibleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $defaultAnswers  = new MultipleAnswers([new SingleAnswer('An answer.')]);

        $checklist          = new ChecklistQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentChecklist = new ChecklistQuestion('Another question?', $possibleAnswers, $defaultAnswers);

        $this->assertFalse($checklist->equals($differentChecklist));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_different_default_answers()
    {
        $defaultAnswers           = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $possibleAnswers          = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $differentPossibleAnswers = new MultipleAnswers(
            [
                new SingleAnswer('An answer.'),
                new SingleAnswer('Another answer.'),
            ]
        );

        $checklist          = new ChecklistQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentChecklist = new ChecklistQuestion('The question?', $differentPossibleAnswers, $defaultAnswers);

        $this->assertFalse($checklist->equals($differentChecklist));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_another_question_with_different_possible_answers()
    {
        $possibleAnswers         = new MultipleAnswers(
            [
                new SingleAnswer('An answer.'),
                new SingleAnswer('Another answer.'),
            ]
        );
        $defaultAnswers          = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $differentDefaultAnswers = new MultipleAnswers([new SingleAnswer('Another answer.')]);

        $checklist          = new ChecklistQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentChecklist = new ChecklistQuestion('The question?', $possibleAnswers, $differentDefaultAnswers);

        $this->assertFalse($checklist->equals($differentChecklist));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_does_not_equal_a_question_of_a_different_type()
    {
        $checklist           = new ChecklistQuestion('The question?', new MultipleAnswers([]), new MultipleAnswers([]));
        $otherTypeOfQuestion = new OpenQuestion('The question?', new SingleAnswer('Default answer'));

        $this->assertFalse($checklist->equals($otherTypeOfQuestion));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_equals_another_question()
    {
        $multipleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);

        $checklist = new ChecklistQuestion('The question?', $multipleAnswers, $multipleAnswers);
        $same      = new ChecklistQuestion('The question?', $multipleAnswers, $multipleAnswers);

        $this->assertTrue($checklist->equals($same));
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_has_a_question_value()
    {
        $expectedQuestionValue = 'The question?';

        $multipleAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $question = new ChecklistQuestion($expectedQuestionValue, $multipleAnswers, $multipleAnswers);
        $actualQuestionValue = $question->getQuestion();

        $this->assertEquals($expectedQuestionValue, $actualQuestionValue);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_has_a_default_answer()
    {
        $expectedDefaultAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);

        $possibleAnswers = new MultipleAnswers([new SingleAnswer('An answer.'), new SingleAnswer('Another answer')]);
        $question = new ChecklistQuestion('A question?', $possibleAnswers, $expectedDefaultAnswers);
        $actualDefaultAnswers = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultAnswers, $actualDefaultAnswers);
    }

    /**
     * @test
     * @group Core
     * @group Interviewer
     * @group Question
     */
    public function question_has_possible_answers()
    {
        $expectedPossibleAnswers = new MultipleAnswers([new SingleAnswer('An answer.'), new SingleAnswer('Another answer')]);

        $defaultAnswers = new MultipleAnswers([new SingleAnswer('An answer.')]);
        $question = new ChecklistQuestion('A question?', $expectedPossibleAnswers, $defaultAnswers);
        $actualPossibleAnswers = $question->getPossibleAnswers();

        $this->assertEquals($expectedPossibleAnswers, $actualPossibleAnswers);
    }
}
