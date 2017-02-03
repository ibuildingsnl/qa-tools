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
assertFileExists('tests/bootstrap.php');
assertFileContains('tests/bootstrap.php', "<?php");

assertFileExists('build.xml');
assertFileContains('build.xml', PhpUnit::ANT_TARGET);

assertFileContains('build.xml', PhpParallelLint::ANT_TARGET_FULL);
assertFileContains('build.xml', 'vendor/bin/parallel-lint --exclude vendor -e php,module,inc,theme,profile,install');

assertFileContains('build.xml', PhpParallelLint::ANT_TARGET_DIFF);
assertFileContains('build.xml', "git diff --cached --name-only --  '*.php' '*.module' '*.inc' '*.theme' '*.profile' '*.install' | ");

assertFileContains('build.xml', PhpCs::ANT_TARGET);
assertFileContains('build.xml', "--extensions=php/php,module/php,inc/php,install/php,profile/php,theme/php");

assertFileContains('build.xml', PhpMd::ANT_TARGET);
assertFileContains('build.xml', "--suffixes php,module,inc,theme,profile,install");
