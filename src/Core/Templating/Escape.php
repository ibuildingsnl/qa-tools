<?php

namespace Ibuildings\QaTools\Core\Templating;

use Twig_Environment as Environment;

final class Escape
{
    /**
     * @param Environment $environment
     * @param string      $stringToEscape
     * @param string      $charset
     * @return string
     */
    public static function xml(Environment $environment, $stringToEscape, $charset)
    {
        return strtr(
            $stringToEscape,
            [
                '"' => '&quot;',
                "'" => '&apos;',
                '<' => '&lt;',
                '>' => '&gt;',
                '&' => '&amp;',
            ]
        );
    }
}
