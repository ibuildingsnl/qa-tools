<?php

namespace Ibuildings\QA\Tools\Common\DependencyInjection;

/**
 * @todo Convert this to a real dependency injection system.
 */
class Twig
{
    /**
     * @return \Twig_Environment
     */
    public function create()
    {
        $loader = new \Twig_Loader_Filesystem(PACKAGE_BASE_DIR . '/config-dist');
        $twig = new \Twig_Environment($loader);
        $filter = new \Twig_SimpleFilter(
            'bool',
            function ($value) {
                if ($value) {
                    return 'true';
                } else {
                    return 'false';
                }
            }
        );
        $twig->addFilter($filter);

        return $twig;
    }
}