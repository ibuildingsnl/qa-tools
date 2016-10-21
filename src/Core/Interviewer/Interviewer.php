<?php
namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Interviewer\Answer\Answer;
use Ibuildings\QaTools\Core\Interviewer\Question\Question as QuestionInterface;

interface Interviewer
{
    /**
     * @param QuestionInterface $question
     * @return Answer
     */
    public function ask(QuestionInterface $question);

    /**
     * Notifies the interviewee of some important information they must notice.
     *
     * @param string $sentence
     * @return void
     */
    public function notice($sentence);

    /**
     * Notifies the interviewee of some details.
     *
     * @param string $sentence
     * @return void
     */
    public function giveDetails($sentence);

    /**
     * Notifies the interviewee of a succes.
     *
     * @param string $sentence
     * @return void
     */
    public function success($sentence);

    /**
     * Warns the interviewee of something.
     *
     * @param string $sentence
     * @return void
     */
    public function warn($sentence);
}
