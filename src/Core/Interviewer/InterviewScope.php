<?php

namespace Ibuildings\QaTools\Core\Interviewer;

use Ibuildings\QaTools\Core\Assert\Assertion;

final class InterviewScope
{
    /**
     * @var string
     */
    private $interviewScope;

    public function __construct($interviewScope)
    {
        Assertion::string($interviewScope);
        $this->interviewScope = $interviewScope;
    }

    /**
     * @param InterviewScope $other
     * @return boolean
     */
    public function equals(InterviewScope $other)
    {
        return $this->interviewScope === $other->interviewScope;
    }

    /**
     * @return string
     */
    public function getInterviewScope()
    {
        return $this->interviewScope;
    }

    /**
     * @return string
     */
    public function calculateHash()
    {
        return md5($this->interviewScope);
    }
}
