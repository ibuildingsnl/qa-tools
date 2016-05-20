<?php

use Ibuildings\QaTools\Core\Exception\LogicException as LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\MissingAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\Question\ChecklistQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class ChecklistQuestionTest extends TestCase
{
    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     *
     * @dataProvider \Ibuildings\QaTools\TestDataProvider::notString()
     */
    public function checklist_questions_question_can_only_be_string($value)
    {
        $this->expectException(InvalidArgumentException::class);

        $multipleAnswers = new Choices([new TextualAnswer('An answer.')]);

        new ChecklistQuestion($value, $multipleAnswers, $multipleAnswers);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_questions_choices_default_to_missing_answer_if_none_given()
    {
        $expectedDefaultAnswer = new MissingAnswer;

        $question = new ChecklistQuestion('A question?', new Choices([new TextualAnswer('An answer.')]));

        $this->assertEquals($expectedDefaultAnswer, $question->getDefaultAnswer());
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_cannot_have_default_choices_that_are_not_possible_choices()
    {
        $this->expectException(LogicException::class);

        $possibleAnswers = new Choices([new TextualAnswer('An answer')]);
        $defaultAnswers  = new Choices([new TextualAnswer('A different answer')]);

        new ChecklistQuestion('A question?', $possibleAnswers, $defaultAnswers);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_does_not_equal_another_checklist_with_a_different_question()
    {
        $possibleAnswers = new Choices([new TextualAnswer('An answer.')]);
        $defaultAnswers  = new Choices([new TextualAnswer('An answer.')]);

        $checklist          = new ChecklistQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentChecklist = new ChecklistQuestion('Another question?', $possibleAnswers, $defaultAnswers);

        $this->assertFalse($checklist->equals($differentChecklist));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_does_not_equal_another_checklist_with_different_default_choices()
    {
        $defaultAnswers           = new Choices([new TextualAnswer('An answer.')]);
        $possibleAnswers          = new Choices([new TextualAnswer('An answer.')]);
        $differentPossibleAnswers = new Choices(
            [
                new TextualAnswer('An answer.'),
                new TextualAnswer('Another answer.'),
            ]
        );

        $checklist          = new ChecklistQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentChecklist = new ChecklistQuestion('The question?', $differentPossibleAnswers, $defaultAnswers);

        $this->assertFalse($checklist->equals($differentChecklist));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_does_not_equal_another_checklist_with_different_possible_choices()
    {
        $possibleAnswers         = new Choices(
            [
                new TextualAnswer('An answer.'),
                new TextualAnswer('Another answer.'),
            ]
        );
        $defaultAnswers          = new Choices([new TextualAnswer('An answer.')]);
        $differentDefaultAnswers = new Choices([new TextualAnswer('Another answer.')]);

        $checklist          = new ChecklistQuestion('The question?', $possibleAnswers, $defaultAnswers);
        $differentChecklist = new ChecklistQuestion('The question?', $possibleAnswers, $differentDefaultAnswers);

        $this->assertFalse($checklist->equals($differentChecklist));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_equals_another_checklist()
    {
        $choices = new Choices([new TextualAnswer('An answer.')]);

        $checklist     = new ChecklistQuestion('The question?', $choices, $choices);
        $sameChecklist = new ChecklistQuestion('The question?', $choices, $choices);

        $this->assertTrue($checklist->equals($sameChecklist));
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_has_a_question_value()
    {
        $expectedQuestionValue = 'The question?';

        $choices             = new Choices([new TextualAnswer('An answer.')]);
        $checklist           = new ChecklistQuestion($expectedQuestionValue, $choices, $choices);
        $actualQuestionValue = $checklist->getQuestion();

        $this->assertEquals($expectedQuestionValue, $actualQuestionValue);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_has_default_choices()
    {
        $expectedDefaultChoices = new Choices([new TextualAnswer('An answer.')]);

        $possibleChoices      = new Choices([new TextualAnswer('An answer.'), new TextualAnswer('Another answer')]);
        $question             = new ChecklistQuestion('A question?', $possibleChoices, $expectedDefaultChoices);
        $actualDefaultChoices = $question->getDefaultAnswer();

        $this->assertEquals($expectedDefaultChoices, $actualDefaultChoices);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_has_possible_choices()
    {
        $expectedPossibleAnswers = new Choices([new TextualAnswer('An answer.'), new TextualAnswer('Another answer')]);

        $defaultAnswers        = new Choices([new TextualAnswer('An answer.')]);
        $question              = new ChecklistQuestion('A question?', $expectedPossibleAnswers, $defaultAnswers);
        $actualPossibleAnswers = $question->getPossibleChoices();

        $this->assertEquals($expectedPossibleAnswers, $actualPossibleAnswers);
    }

    /**
     * @test
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_can_suggest_given_compatible_choices_that_are_possible_choices_as_default_answer()
    {
        $expectedDefaultChoices = new Choices([new TextualAnswer('Another answer')]);

        $question        = new ChecklistQuestion(
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
     * @group Conversation
     * @group Interviewer
     * @group Question
     */
    public function checklist_cannot_suggest_given_compatible_choices_that_are_not_possible_choices_as_default_answer()
    {
        $this->expectException(LogicException::class);

        $impossibleDefaultChoices = new Choices([new TextualAnswer('Not possible answer')]);

        $question = new ChecklistQuestion(
            'A question?',
            new Choices([new TextualAnswer('An answer')]),
            new Choices([new TextualAnswer('An answer')])
        );
        $question->withDefaultAnswer($impossibleDefaultChoices);
    }
}
