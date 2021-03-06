<?php

namespace Ibuildings\QaTools\Core\Templating;

use Ibuildings\QaTools\Core\Application\Basedir;
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
     * @param string $path A path relative to the application entrypoint `./bin/qa-tools`.
     */
    public function setPath($path)
    {
        Assertion::string($path);

        $this->twig->setLoader(new Twig_Loader_Filesystem(Basedir::get() . '/'. $path));
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
