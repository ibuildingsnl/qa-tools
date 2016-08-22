Writing system tests
====================

The QA Tools project employs the [expect][man-expect] tool to perform system
testing against the phar file that is distributed to developers. Contributors
can describe their expectations of the interactive dialogue with the tool and
assert what the program results are. Together, these form a *specification*.

System specifications consist of two parts, a *dialogue expectation* and a
*test file*. These must be placed in the directory `./tests/system/specs`.

    .
    ├── tests
    │   ├── system
    │   │   ├── specs
    │   │   │   ├── reticulates-splines.php # Assertions
    │   │   │   └── reticulates-splines.tcl # Dialogue expectation

The specification is executed in a temporary directory. Initially, this
directory contains symbolic links to the distributable and its public key. This
is also the directory where QA Tools will generate files.

    /tmp
    ├── qa-tools_system5790bd1c4b5c68.27898406
    │   ├── qa-tools
    │   └── qa-tools.pubkey

[man-expect]: http://linux.die.net/man/1/expect

## The dialogue expectation

The dialogue expectations are written in [TCL][wiki-tcl]. No comprehensive
knowledge of TCL is required to write these dialogue expectations.

First, you indicate what QA Tools subcommand must be executed with which
arguments. It is important you specify that no ANSI colouring be included in the
QA Tools' output.

```tcl
test ./qa-tools configure --no-ansi
```

For each interactive question, state the expected question text, and the answer
`expect` should send in response. The expectation times out after a
[hard-coded][expectation-harness] 2 seconds.

```tcl
answer "What is the project's name?" with "Wobbly Widdershins"
```

The intermediate presence of a string can also be asserted.

```tcl
should_see "Reticulating splines..."
```

`answer` and `should_see` are procedures defined in the
[expectation harness][expectation-harness] so that expressive dialogue
expectations can be written.

Finally, you can assert that the program will end and will exit with a zero
exit code. If the program does not end within the configured time-out the expect
script exits with exit code 1. When the program exits with a non-zero exit code,
the expect script exits with the same exit code.

```tcl
exits_with 0
```

[wiki-tcl]: https://en.wikipedia.org/wiki/Tcl
[expectation-harness]: ../../tests/system/harness.tcl

### Pitfalls

One pitfall is the use of brackets in strings; these execute commands. The below
snippet, expecting a specific multiple-choice answer, attempts to execute `0`:

```tcl
answer "[0] Symfony 3" with "0"
```

The solution is to escape the brackets:

```tcl
answer "\[0\] Symfony 3" with "0"
```

## The test file

The test file contains plain PHP. The expect script is available as the callable
`$expect`. This enables you to arrange things before executing QA Tools, like
adding conflicts to the project's Composer configuration. Note that the test
file ought to reside in the namespace `Ibuildings\QaTools\SystemTest`; this 
makes sure PHPUnit's assertion functions are in scope.

```php
<?php

namespace Ibuildings\QaTools\SystemTest;

Composer::initialise();

/** @var callable $expect */
$expect();

assertFileExists('qa-tools.json');
Composer::assertPackageIsInstalled(new PackageName('phpmd/phpmd'));
```

## Example specification

```tcl
spawn ./qa-tools configure --no-ansi

should_see "Configuring the Ibuildings QA Tools"

answer "What is the project's name?" with "Boolean Bust"

answer "Where would you like to store the generated files?" with "./"

should_see "What type of project would you like to configure?"
answer "\[0\] PHP" with "0"

should_see "What type of PHP project would you like to configure?"
answer "\[1\] Symfony 3" with "1"

answer "Would you like to integrate Travis in your project?" with "Y"

exits_with 0
```

```php
<?php

namespace Ibuildings\QaTools\SystemTest;

Composer::initialise();

/** @var callable $expect */
$expect();

assertFileExists('qa-tools.json');
Composer::assertPackageIsInstalled('phpmd/phpmd');
```

## Installing Composer packages

Most tools will want to install Composer packages. However, installing these
packages during testing, both locally as on Travis, makes tests slow and
brittle. To countermand this, all tests involving Composer (pretty much all
system tests) work with locally emulated packages. An example of this is
`phpmd/phpmd`, emulated in
[`tests/composer/packages/phpmd/composer.json`](../../tests/composer/packages/phpmd/composer.json).
These emulated packages are registered with Composer in an initialisation stage
in each test by calling [Composer::initialise](../../tests/system/Composer.php).
