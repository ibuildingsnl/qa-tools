<?php

namespace Ibuildings\QaTools\Core\Templating;

use Ibuildings\QaTools\Core\Assert\Assertion;
use Twig_Environment;
use Twig_Loader_Filesystem;

class TemplateEngine
{
    /**
     * @var Twig_Environment|null
     */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param string $relativePath
     */
    public function setPath($relativePath)
    {
        Assertion::string($relativePath);

        $this->twig->setLoader(new Twig_loader_Filesystem(APPLICATION_ROOT_DIR . '/' . $relativePath));
    }

    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    public function render($template, array $params)
    {
        Assertion::string($template);

        return $this->twig->render($template, $params);
    }
}
