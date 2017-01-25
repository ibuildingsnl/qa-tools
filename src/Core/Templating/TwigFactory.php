<?php

namespace Ibuildings\QaTools\Core\Templating;

use Twig_Environment as Environment;
use Twig_Extension_Core as CoreExtension;

final class TwigFactory
{
    /**
     * @return Environment
     */
    public static function create()
    {
        $twig = new Environment(null, [
            'strict_variables' => true,
        ]);

        /** @var CoreExtension $coreExtension */
        $coreExtension = $twig->getExtension('core');
        $coreExtension->setEscaper('xml', [Escape::class, 'xml']);

        return $twig;
    }
}
