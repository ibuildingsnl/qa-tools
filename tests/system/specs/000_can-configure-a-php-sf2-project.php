<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Core\Composer\PackageName;

Composer::initialise(new PackageName('my/project'));

/** @var callable $expect */
$expect();

assertFileExists('qa-tools.json');
Composer::assertPackageIsInstalled(new PackageName('phpmd/phpmd'));
