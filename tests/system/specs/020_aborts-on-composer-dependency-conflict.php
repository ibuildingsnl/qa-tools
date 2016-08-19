<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Core\Composer\Package;
use Ibuildings\QaTools\Core\Composer\PackageName;

Composer::initialise(new PackageName('my/project'));
Composer::addConflict(Package::of('phpmd/phpmd', '*'));

/** @var callable $expect */
$expect();

assertFileNotExists('qa-tools.json');
Composer::assertPackageIsNotInstalled(new PackageName('phpmd/phpmd'));
