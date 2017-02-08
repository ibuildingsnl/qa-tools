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
assertFileNotExists('tests/bootstrap.php');

assertFileExists('build.xml');
assertFileContains('build.xml', PhpUnit::ANT_TARGET);

assertFileContains('build.xml', PhpLint::ANT_TARGET_FULL);
assertFileContains('build.xml', "find ./  -type f -name '*.php' ! -path './vendor/*' | ");

assertFileContains('build.xml', PhpLint::ANT_TARGET_DIFF);
assertFileContains('build.xml', "git diff --cached --name-only --  '*.php' | ");

assertFileContains('build.xml', PhpCs::ANT_TARGET);
assertFileContains('build.xml', '"--extensions=php/php"');

assertFileContains('build.xml', PhpMd::ANT_TARGET);
assertFileContains('build.xml', '"--suffixes php"');
