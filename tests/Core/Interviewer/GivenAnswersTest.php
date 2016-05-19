<?php

use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\GivenAnswers;
use Ibuildings\QaTools\Core\Interviewer\InterviewScope;
use Ibuildings\QaTools\Core\Interviewer\Question\ChecklistQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class GivenAnswersTest extends TestCase
{
    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     */
    public function an_exception_is_thrown_if_an_answer_is_requested_for_a_question_that_has_not_been_answered()
    {
        $this->expectException(LogicException::class);

        $nonExistentQuestion = new TextualQuestion('Can this question be found?');
        $irrelevantScope     = new InterviewScope('SomeClassNameScope');

        $givenAnswers = GivenAnswers::withoutAnswers();
        $givenAnswers->getGivenAnswerFor($nonExistentQuestion, $irrelevantScope);
    }

    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     */
    public function an_answer_can_be_given_and_queried_regarding_a_specific_scope_and_question()
    {
        $expectedAnswer = YesOrNoAnswer::yes();
        $question = new YesOrNoQuestion('Is this a question?');
        $irrelevantScope = new InterviewScope('SomeClassNameScope');

        $givenAnswers = GivenAnswers::withoutAnswers();
        $givenAnswers->giveAnswerFor($question, $expectedAnswer, $irrelevantScope);

        $this->assertEquals($expectedAnswer, $givenAnswers->getGivenAnswerFor($question, $irrelevantScope));
    }

    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     */
    public function given_answers_are_correctly_serialized_to_json()
    {
        $irrelevantScope = new InterviewScope('SomeClassNameScope');
        $scopeHash       = $irrelevantScope->calculateHash();

        $yesOrNoQuestion = new YesOrNoQuestion('Yes or no question?');
        $yesOrNoAnswer   = YesOrNoAnswer::yes();
        $yesOrNoHash     = $yesOrNoQuestion->calculateHash() . $scopeHash;

        $textualQuestion = new TextualQuestion('Textual question?');
        $textualAnswer   = new TextualAnswer('Textual answer');
        $textualHash     = $textualQuestion->calculateHash() . $scopeHash;

        $multipleChoiceQuestion = new MultipleChoiceQuestion(
            'Multiple choice question',
            new Choices([new TextualAnswer('Multiple choice answer')])
        );
        $multipleChoiceAnswer   = new TextualAnswer('Multiple choice answer');
        $multipleChoiceHash     = $multipleChoiceQuestion->calculateHash() . $scopeHash;

        $checklistWithOneAnswerQuestion = new ChecklistQuestion(
            'Checklist question?',
            new Choices([new TextualAnswer('Checklist answer')])
        );
        $checklistWithOneAnswerAnswer   = new Choices([new TextualAnswer('Checklist answer')]);
        $checklistWithOneAnswerHash     = $checklistWithOneAnswerQuestion->calculateHash() . $scopeHash;

        $checklistWithMultipleAnswersQuestion = new ChecklistQuestion(
            'Another checklist question?',
            new Choices([new TextualAnswer('Checklist answer'), new TextualAnswer('Another checklist answer')])
        );
        $checklistWithMultipleAnswersAnswer = new Choices(
            [
                new TextualAnswer('Checklist answer'),
                new TextualAnswer('Another checklist answer'),
            ]
        );
        $checklistWithMultipleAnswersHash = $checklistWithMultipleAnswersQuestion->calculateHash() . $scopeHash;

        $givenAnswers = GivenAnswers::withoutAnswers();
        $givenAnswers->giveAnswerFor($yesOrNoQuestion, $yesOrNoAnswer, $irrelevantScope);
        $givenAnswers->giveAnswerFor($textualQuestion, $textualAnswer, $irrelevantScope);
        $givenAnswers->giveAnswerFor($multipleChoiceQuestion, $multipleChoiceAnswer, $irrelevantScope);
        $givenAnswers->giveAnswerFor($checklistWithOneAnswerQuestion, $checklistWithOneAnswerAnswer, $irrelevantScope);
        $givenAnswers->giveAnswerFor(
            $checklistWithMultipleAnswersQuestion,
            $checklistWithMultipleAnswersAnswer,
            $irrelevantScope
        );

        $expectedJsonString = sprintf(
            '{"%s":true,"%s":"Textual answer","%s":"Multiple choice answer","%s":["Checklist answer"],"%s":["Checklist answer","Another checklist answer"]}',
            $yesOrNoHash,
            $textualHash,
            $multipleChoiceHash,
            $checklistWithOneAnswerHash,
            $checklistWithMultipleAnswersHash
        );

        $this->assertEquals($expectedJsonString, $givenAnswers->serialize());
    }

    /**
     * @test
     * @group Interview
     * @group GivenAnswers
     */
    public function given_answers_are_deserialized_from_an_array_of_json_data_correctly()
    {
        $irrelevantScope = new InterviewScope('SomeClassNameScope');
        $scopeHash       = $irrelevantScope->calculateHash();

        $yesOrNoQuestion = new YesOrNoQuestion('Yes or no question?');
        $yesOrNoAnswer   = YesOrNoAnswer::yes();
        $yesOrNoHash     = $yesOrNoQuestion->calculateHash() . $scopeHash;

        $textualQuestion = new TextualQuestion('Textual question?');
        $textualAnswer   = new TextualAnswer('Textual answer');
        $textualHash     = $textualQuestion->calculateHash() . $scopeHash;

        $multipleChoiceQuestion = new MultipleChoiceQuestion(
            'Multiple choice question',
            new Choices([new TextualAnswer('Multiple choice answer')])
        );
        $multipleChoiceAnswer   = new TextualAnswer('Multiple choice answer');
        $multipleChoiceHash     = $multipleChoiceQuestion->calculateHash() . $scopeHash;

        $checklistWithOneAnswerQuestion = new ChecklistQuestion(
            'Checklist question?',
            new Choices([new TextualAnswer('Checklist answer')])
        );
        $checklistWithOneAnswerAnswer   = new Choices([new TextualAnswer('Checklist answer')]);
        $checklistWithOneAnswerHash     = $checklistWithOneAnswerQuestion->calculateHash() . $scopeHash;

        $checklistWithMultipleAnswersQuestion = new ChecklistQuestion(
            'Another checklist question?',
            new Choices([new TextualAnswer('Checklist answer'), new TextualAnswer('Another checklist answer')])
        );
        $checklistWithMultipleAnswersAnswer = new Choices(
            [
                new TextualAnswer('Checklist answer'),
                new TextualAnswer('Another checklist answer'),
            ]
        );
        $checklistWithMultipleAnswersHash = $checklistWithMultipleAnswersQuestion->calculateHash() . $scopeHash;

        $expectedGivenAnswers = GivenAnswers::withoutAnswers();
        $expectedGivenAnswers->giveAnswerFor($yesOrNoQuestion, $yesOrNoAnswer, $irrelevantScope);
        $expectedGivenAnswers->giveAnswerFor($textualQuestion, $textualAnswer, $irrelevantScope);
        $expectedGivenAnswers->giveAnswerFor($multipleChoiceQuestion, $multipleChoiceAnswer, $irrelevantScope);
        $expectedGivenAnswers->giveAnswerFor($checklistWithOneAnswerQuestion, $checklistWithOneAnswerAnswer, $irrelevantScope);
        $expectedGivenAnswers->giveAnswerFor(
            $checklistWithMultipleAnswersQuestion,
            $checklistWithMultipleAnswersAnswer,
            $irrelevantScope
        );

        $givenJsonString = sprintf(
            '{"%s":true,"%s":"Textual answer","%s":"Multiple choice answer","%s":["Checklist answer"],"%s":["Checklist answer","Another checklist answer"]}',
            $yesOrNoHash,
            $textualHash,
            $multipleChoiceHash,
            $checklistWithOneAnswerHash,
            $checklistWithMultipleAnswersHash
        );
        $actualGivenAnswers = GivenAnswers::deserialize(json_decode($givenJsonString, true));

        $this->assertEquals($expectedGivenAnswers, $actualGivenAnswers);
    }
}
