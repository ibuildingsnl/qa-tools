<?php

namespace Ibuildings\QaTools\Core\Configuration;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Ibuildings\QaTools\Core\Templating\TemplateEngine;

class TaskHelperSet
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
        Assertion::nonEmptyString($template, 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'template');

        return $this->templateEngine->render($template, $params);
    }

    /**
     * @param string $path
     */
    public function setTemplatePath($path)
    {
        Assertion::nonEmptyString($path, 'Expected non-empty string for "%3$s", got "%s" of type "%s"', 'path');

        $this->templateEngine->setPath($path);
    }
}
