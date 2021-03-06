<?php

namespace Ibuildings\QaTools\SystemTest;

use Ibuildings\QaTools\Tool\PhpCs\PhpCs;
use Ibuildings\QaTools\Tool\PhpParallelLint\PhpParallelLint;
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

assertFileContains('build.xml', PhpParallelLint::ANT_TARGET_FULL);
assertFileContains('build.xml', 'executable="vendor/bin/parallel-lint"');
assertFileContains('build.xml', '<arg value="php"');

assertFileContains('build.xml', PhpParallelLint::ANT_TARGET_DIFF);
assertFileContains('build.xml', "git diff --cached --name-only --diff-filter=d --  '*.php' | ");

assertFileContains('build.xml', PhpCs::ANT_TARGET);
assertFileContains('build.xml', '"--extensions=php/php"');
assertFileExists('ruleset.xml');
assertFileContains('ruleset.xml', '<rule ref="vendor/escapestudios/symfony2-coding-standard/Symfony2"');
assertFileContains('ruleset.xml', '<property name="lineLimit" value="120"');
assertFileContains('ruleset.xml', '<property name="absoluteLineLimit" value="150"');

assertFileContains('build.xml', PhpMd::ANT_TARGET);
assertFileContains('build.xml', '"--suffixes php"');
