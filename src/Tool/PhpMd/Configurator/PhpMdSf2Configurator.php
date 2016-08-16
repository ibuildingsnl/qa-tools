<?php

namespace Ibuildings\QaTools\Tool\PhpMd\Configurator;

use Ibuildings\QaTools\Core\Configuration\TaskDirectory;
use Ibuildings\QaTools\Core\Configuration\TaskHelperSet;
use Ibuildings\QaTools\Core\Configurator\Configurator;
use Ibuildings\QaTools\Core\Interviewer\Interviewer;
use Ibuildings\QaTools\Core\Task\Composer\Package;
use Ibuildings\QaTools\Core\Task\RequireComposerPackagesTask;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;

final class PhpMdSf2Configurator implements Configurator
{
    public function configure(Interviewer $interviewer, TaskDirectory $taskDirectory, TaskHelperSet $taskHelperSet)
    {
        $packagePhpMd2 = new Package('phpmd/phpmd', '^2.0');
        $taskDirectory->registerTask(new RequireComposerPackagesTask($packagePhpMd2), $this->getToolClassName());
    }

    /**
     * @return string
     */
    public function getToolClassName()
    {
        return PhpMd::class;
    }
}
