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
acceptance testing, security testing, and smoke testing.

You'll find these tests in the following locations:

| **Type of tests**           | **Location**          |
|-----------------------------|-----------------------|
| Unit tests                  | `./tests/unit`        |
| Integration tests           | `./tests/integration` |
| Acceptance tests            | `./features`          |
| Security tests              | `./tests/security`    |
| Smoke tests                 | `./tests/smoke`    |

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
functional testing: unit, integration, and acceptance testing.

It is important that the workings of the [tools](docs/ubiquitous-language.md)
are proven using acceptance tests. During the implementation of each feature,
the need for unit and integration tests will probably arise. Certain glue code,
like bundles and extensions in a typical Symfony application, may not require
testing. The QA Tools core will probably be covered mostly by unit and
integration tests.

Furthermore, to make sure users can verify they have an authentic release of the
QA Tools, and that we're not shipping with any catalogued security
vulnerabilities, we perform security testing.

Lastly, we perform smoke tests as a final check before clearing the phar for
distribution.
