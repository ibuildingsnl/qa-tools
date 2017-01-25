<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Tool\PhpCs\PhpCs;
use Ibuildings\QaTools\Tool\PhpLint\PhpLint;
use Ibuildings\QaTools\Tool\PhpMd\PhpMd;
use Ibuildings\QaTools\Tool\PhpUnit\PhpUnit;
use Ibuildings\QaTools\Tool\SensioLabsSecurityChecker\SensioLabsSecurityChecker;

Composer::initialise();

/** @var callable $expect */
$expect();

assertFileExists('qa-tools.json');

Composer::assertPackageIsInstalled('phpunit/phpunit');
assertFileExists('phpunit.xml');
assertFileExists('tests/bootstrap.php');
assertFileContains('tests/bootstrap.php', "<?php");

assertFileExists('build.xml');
assertFileContains('build.xml', PhpUnit::ANT_TARGET);
