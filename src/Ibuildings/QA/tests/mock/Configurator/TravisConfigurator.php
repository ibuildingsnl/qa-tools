<?php

namespace Ibuildings\QA\tests\mock\Configurator;

use Ibuildings\QA\Tools\Common\Configurator\TravisConfigurator as BaseConfigurator;

class TravisConfigurator extends BaseConfigurator
{
    public $travisFileContents;
    public $travisPhpIniContents;

    public function writeConfig()
    {
        // not sure if shouldWrite is called, this can be removed if so
        if (!$this->shouldWrite()) {
            return;
        }

        $this->travisFileContents = $this->twig->render('.travis.yml.dist', $this->settings['travis']);
        $this->travisPhpIniContents = $this->twig->render('.travis.php.ini.dist');

        $this->output->writeln("\n<info>Files for Travis integration have been written</info>");
    }
}
