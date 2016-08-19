<?php

namespace Ibuildings\QaTools\UnitTest\Core\Task;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Task\Task;

final class FakeTask implements Task
{
    /**
     * @var string
     */
    private $string;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        Assertion::string($string);
        $this->string = $string;
    }

    public function getDescription()
    {
        return sprintf('FakeTask(%s)', $this->string);
    }

    public function checkPrerequisites(Interviewer $interviewer)
    {
    }

    public function execute(Interviewer $interviewer)
    {
    }

    public function rollBack(Interviewer $interviewer)
    {
    }

    public function equals(Task $task)
    {
        /** @var self $task */
        return get_class($this) === get_class($task) && $this->string === $task->string;
    }
}
