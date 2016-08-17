<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;

class RequirementHelperSet
{
    /**
     * @var TemplateEngine
     */
    private $templateEngine;

    public function __construct(TemplateEngine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    public function renderTemplate($template, array $params = [])
    {
        Assertion::nonEmptyString($template, 'template');

        return $this->templateEngine->render($template, $params);
    }

    /**
     * @param string $path
     */
    public function setTemplatePath($path)
    {
        Assertion::nonEmptyString($path, 'path');

        $this->templateEngine->setPath($path);
    }
}
