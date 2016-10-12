<?php
namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Build\Target;

final class AddBuildTask implements Task
{
    /**
     * @var Snippet
     */
    private $snippet;

    /**
     * @var Tool
     */
    private $tool;

    /**
     * @var Target
     */
    private $target;

    /**
     * @param Target  $target
     * @param Tool    $tool
     * @param Snippet $snippet
     */
    public function __construct(Target $target, Tool $tool, Snippet $snippet)
    {
        $this->tool = $tool;
        $this->snippet = $snippet;
        $this->target = $target;
    }

    /**
     * @return Snippet
     */
    public function getSnippet()
    {
        return $this->snippet;
    }

    /**
     * @return Tool
     */
    public function getTool()
    {
        return $this->tool;
    }

    /**
     * @return Target
     */
    public function getTarget()
    {
        return $this->target;
    }
}
