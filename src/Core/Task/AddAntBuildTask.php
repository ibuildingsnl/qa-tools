<?php
namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;
use Ibuildings\QaTools\Core\Build\Target;

final class AddAntBuildTask implements Task
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
     * @param AddAntBuildTask $other
     * @param array           $prioritizedTools
     * @return int
     */
    public function prioritize(AddAntBuildTask $other, array $prioritizedTools)
    {
        return $this->tool->compare($other->tool, $prioritizedTools);
    }

    /**
     * @return string
     */
    public function getSnippetContents()
    {
        return $this->snippet->getContents();
    }

    /**
     * @return string
     */
    public function getSnippetTargetIdentifier()
    {
        return $this->snippet->getTarget();
    }

    /**
     * @param Target $target
     * @return bool
     */
    public function hasTarget(Target $target)
    {
        return $this->target->equals($target);
    }

    /**
     * @param AddAntBuildTask $other
     * @return bool
     */
    public function equals(AddAntBuildTask $other)
    {
        return $this->target->equals($other->target)
            && $this->tool->equals($other->tool)
            && $this->snippet->equals($other->snippet);
    }
}
