Contributing guidelines
=======================

## Code style

All code of the QA Tools proper must adhere to the [PSR-2 style guide][psr2].

[psr2]: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md

## Testing

Testing is important to the QA Tools project; once a release is delivered, it
ought to work properly, or users may have to manually roll back to a previous
release.

Before submitting your contribution, please add tests you think are relevant and
run the test suite so you're sure you haven't broken any existing functionality.

To run the test suite, you need the following:

 * `php`* (^5.6|^7.0)
 * `composer`*
 * `make`* (3.81, 4.1)
 * Unix core utils (`rm`, `echo`, BSD/GNU `grep`, `test`, `cp`, `cat`, `sed`, `column`)*
 * `openssl`*
 * `expect`* (5.45) (`apt install expect` on Ubuntu, already installed on Mac)

> *) Available in your PATH and executable

```shell-session
$ make test      # All tests, including system tests
$ make test-fast # Only unit, integration, and functional test
```

Until [dogfooding][wiki:dogfooding] is possible, you can install the pre-commit
and pre-push Git hooks to help you test the changes you make:

```shell-session
$ tools/git/pre-commit-install
$ tools/git/pre-push-install
```

If you feel comfortable with testing your contribution, it is good to know that
we perform various types of testing: unit testing, integration testing,
system testing, and security testing.

You'll find these tests in the following locations:

| **Type of tests**           | **Location**          |
|-----------------------------|-----------------------|
| Unit tests                  | `./tests/unit`        |
| Integration tests           | `./tests/integration` |
| System tests                | `./tests/system`      |

The only security test is currently located in the `Makefile` target
`verify-build-is-signed`.

Feel free to read the [Testing strategy](#testing-strategy) to find out what
testing strategy the QA Tools project employs.

[wiki:dogfooding]: https://en.wikipedia.org/wiki/Eating_your_own_dog_food
 
### Testing strategy 

The testing strategy helps to:

 * implement what is needed – and only what is needed – by the users of
   this tool;
 * discover bugs in the software;
 * prevent bugs from occurring again in the future, making it safer to modify
   the code;
 * assure that the software is of quality;
 * assure that the distributables can be verified to originate from the QA Tools
   project;
 * prevent accumulation of technical debt by pointing out complexity;
 * and reduce maintenance cost by catching bugs early and having less technical
   debt.

Because the QA Tools application is distributed as a stand-alone console
application, and because it likely operates on a user's own developer machine,
it is important that the tool works properly. We use the following types of
functional testing: unit, integration, and system testing.

The final distributable is put through its paces using system tests. These tests
configure the QA Tools in fictitious projects and asserts to various degrees
that the tools are correctly configured.

Furthermore, to make sure users can verify they have an authentic release of the
QA Tools, and that we're not shipping with any catalogued security
vulnerabilities, we perform security testing.
