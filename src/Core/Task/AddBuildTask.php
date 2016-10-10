<?php

namespace Ibuildings\QaTools\Core\Task;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Stages\Stage;

final class AddBuildTask implements Task
{
    /**
     * @var string
     */
    private $template;

    /**
     * @var Stage
     */
    private $stage;

    /**
     * @var string
     */
    private $targetName;

    /**
     * @param Stage  $stage
     * @param string $template
     * @param string $targetName
     */
    public function __construct(Stage $stage, $template, $targetName)
    {
        Assertion::string(
            $template,
            sprintf('Template name ought to be a string')
        );

        $this->stage = $stage;
        $this->template = $template;
        $this->targetName = $targetName;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return Stage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * @return string
     */
    public function getTargetName()
    {
        return $this->targetName;
    }

    public function __toString()
    {
        return sprintf(
            'AddBuildTask(stage="%s", template="%s", targetName="%s")',
            get_class($this->stage),
            substr($this->template, 0, 20),
            $this->targetName
        );
    }
}
