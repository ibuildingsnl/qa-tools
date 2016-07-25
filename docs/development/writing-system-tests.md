Writing system tests
====================

The QA Tools project employs the [expect][man-expect] tool to perform system
testing against the phar file that is distributed to developers. Contributors
can describe their expectations of the interactive dialogue with the tool and
assert what the program results are. Together, these form a *specification*.

System specifications consist of two parts, a *dialogue expectation* and an
*assertion file*. These must be placed in the directory `./tests/system/specs`.

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
[hard-coded][expectation-harness] 1 second.

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

## The assertions file

The assertions file contains plain PHP. It is executed after the dialogue has
been executed. The assertion must reside in the namespace
`Ibuildings\QaTools\SystemTest`. PHPUnit's assertion functions are in scope.

```php
<?php

namespace Ibuildings\QaTools\SystemTest;

assertFileExists('qa-tools.json');
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

expect_eof
```

```php
<?php

namespace Ibuildings\QaTools\SystemTest;

assertFileExists('qa-tools.json');
```


