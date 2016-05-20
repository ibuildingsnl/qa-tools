<?php

use Ibuildings\QaTools\Core\Exception\LogicException;
use Ibuildings\QaTools\Core\Interviewer\Answer\Choices;
use Ibuildings\QaTools\Core\Interviewer\Answer\TextualAnswer;
use Ibuildings\QaTools\Core\Interviewer\Answer\YesOrNoAnswer;
use Ibuildings\QaTools\Core\Interviewer\InterviewerInterface;
use Ibuildings\QaTools\Core\Interviewer\MemorizingInterviewer;
use Ibuildings\QaTools\Core\Interviewer\Question\ChecklistQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\MultipleChoiceQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\TextualQuestion;
use Ibuildings\QaTools\Core\Interviewer\Question\YesOrNoQuestion;
use PHPUnit_Framework_TestCase as TestCase;

class MemorizingInterviewerTest extends TestCase
{
    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     *
     * @dataProvider compatibleQuestionsAndAnswersProvider
     */
    public function memorizing_interviewer_remembers_that_an_asked_question_has_been_answered($question, $answer)
    {
        $dummyInterviewer = Mockery::mock(InterviewerInterface::class);

        $irrelevantScope = 'SomeClassScope';
        $hash = md5($question . $irrelevantScope);

        $memorizingInterviewer = new MemorizingInterviewer([$hash => $answer], $dummyInterviewer);
        $memorizingInterviewer->setScope($irrelevantScope);

        $this->assertTrue($memorizingInterviewer->hasPreviousAnswerFor($question));
    }

    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     */
    public function memorizing_interviewer_does_not_remember_answers_to_questions_that_have_not_been_asked()
    {
        $question = new TextualQuestion('The question');

        $dummyInterviewer = Mockery::mock(InterviewerInterface::class);
        $memorizingInterviewer = new MemorizingInterviewer([], $dummyInterviewer);

        $this->assertFalse($memorizingInterviewer->hasPreviousAnswerFor($question));
    }

    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     */
    public function memorizing_interviewer_throws_exception_when_a_question_is_queried_of_which_it_has_no_memory()
    {
        $this->expectException(LogicException::class);

        $question = new TextualQuestion('The question');

        $dummyInterviewer = Mockery::mock(InterviewerInterface::class);
        $memorizingInterviewer = new MemorizingInterviewer([], $dummyInterviewer);

        $memorizingInterviewer->getPreviousAnswerFor($question);
    }

    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     *
     * @dataProvider compatibleQuestionsAndAnswersProvider
     */
    public function memorizing_interviewer_remembers_answers_to_questions($question, $answer)
    {
        $fakeInterviewer = Mockery::mock(InterviewerInterface::class);

        $irrelevantScope = 'SomeClassScope';
        $hash = md5($question . $irrelevantScope);

        $memorizingInterviewer = new MemorizingInterviewer([$hash => $answer], $fakeInterviewer);
        $memorizingInterviewer->setScope($irrelevantScope);

        $this->assertEquals($answer, $memorizingInterviewer->getPreviousAnswerFor($question));
    }

    /**
     * @test
     * @group Interviewer
     * @group GivenAnswers
     *
     */
    public function memorizing_interviewer_forgets_answers_incompatible_with_the_question_they_were_answered_for()
    {

    }

    public function compatibleQuestionsAndAnswersProvider()
    {
        return [
            'textual question and textual answer' =>
                [
                    new TextualQuestion('The question'),
                    new TextualAnswer('The answer'),
                ],
            'yes or no question and positive yes or no answer' =>
                [
                    new YesOrNoQuestion('The question'),
                    YesOrNoAnswer::yes(),
                ],
            'yes or no question and negative yes or no answer' =>
                [
                    new YesOrNoQuestion('The question'),
                    YesOrNoAnswer::no()
                ],
            'multiple choice question and textual answer' =>
                [
                    new MultipleChoiceQuestion('The question', new Choices([new TextualAnswer('An answer')])),
                    new TextualAnswer('An answer'),
                ],
            'checklist question and choices' =>
                [
                    new ChecklistQuestion('The question', new Choices([new TextualAnswer('An answer')])),
                    new Choices([new TextualAnswer('An answer')]),
                ],
        ];
    }

    public function compatibleQuestionsAndAnswersAndIncompatibleAnswerProvider()
    {
        return [
            'textual question vs yes or no question' =>
                [
                    new TextualQuestion('The question'),
                    new TextualAnswer('The answer'),
                    new YesOrNoQuestion('The question'),
                    YesOrNoAnswer::yes(),
                ],
            'textual question vs checklist' =>
                [
                    new TextualQuestion('The question'),
                    new TextualAnswer('The answer'),
                    new Choices([new TextualAnswer('An answer')]),
                ],
            'yes or no question and yes or no answer vs textual answer' =>
                [
                    new YesOrNoQuestion('The question'),
                    YesOrNoAnswer::yes(),
                    new TextualAnswer('The answer'),
                ],
            'yes or no question and yes or no answer vs choices' =>
                [
                    new YesOrNoQuestion('The question'),
                    YesOrNoAnswer::yes(),
                    new Choices([new TextualAnswer('An answer')]),
                ],
            'multiple choice question and textual answer vs yes or no answer' =>
                [
                    new MultipleChoiceQuestion('The question', new Choices([new TextualAnswer('An answer')])),
                    new TextualAnswer('The answer'),
                    YesOrNoAnswer::yes(),
                ],
            'multiple choice question and textual answer vs choices' =>
                [
                    new MultipleChoiceQuestion('The question', new Choices([new TextualAnswer('An answer')])),
                    new TextualAnswer('The answer'),
                    new Choices([new TextualAnswer('An answer')]),
                ],
            'checklist question and choices vs textual answer' =>
                [
                    new ChecklistQuestion('The question', new Choices([new TextualAnswer('An answer')])),
                    new Choices([new TextualAnswer('An answer')]),
                    new TextualAnswer('The answer'),
                ],
            'checklist question and choices vs yes or no answer' =>
                [
                    new ChecklistQuestion('The question', new Choices([new TextualAnswer('An answer')])),
                    new Choices([new TextualAnswer('An answer')]),
                    YesOrNoAnswer::yes(),
                ],
        ];
    }
}
