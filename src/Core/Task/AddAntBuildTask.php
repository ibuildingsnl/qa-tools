<?php
namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Build\Build;
use Ibuildings\QaTools\Core\Build\Snippet;
use Ibuildings\QaTools\Core\Build\Tool;

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
     * @var Build
     */
    private $build;

    /**
     * @param Build   $build
     * @param Tool    $tool
     * @param Snippet $snippet
     */
    public function __construct(Build $build, Tool $tool, Snippet $snippet)
    {
        $this->tool = $tool;
        $this->snippet = $snippet;
        $this->build = $build;
    }

    /**
     * @param AddAntBuildTask $other
     * @param string[]        $toolOrder
     * @return int
     */
    public function compare(AddAntBuildTask $other, array $toolOrder)
    {
        return $this->tool->compare($other->tool, $toolOrder);
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
     * @param Build $build
     * @return bool
     */
    public function hasTarget(Build $build)
    {
        return $this->build->equals($build);
    }

    /**
     * @param AddAntBuildTask $other
     * @return bool
     */
    public function equals(AddAntBuildTask $other)
    {
        return $this->build->equals($other->build)
            && $this->tool->equals($other->tool)
            && $this->snippet->equals($other->snippet);
    }
}
