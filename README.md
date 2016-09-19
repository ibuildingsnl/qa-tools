# Ibuildings QA Tools v3 [![Build Status](https://travis-ci.com/ibuildingsnl/qa-tools-v3.svg?token=JEaBsbhAuRqMRnCxyjuy&branch=master)](https://travis-ci.com/ibuildingsnl/qa-tools-v3)
A set of quality assurance tools that are easily configurable through an installer.

The QA Tools are meant to provide you with a decent base build setup, conforming to Ibuildings standards. 
They are not meant to provide a solution for every use case. If you want a more complex setup,
you can use the resulting configurations as a base and configure it manually.

## Installation

The recommended way to install the QA Tools is by downloading the latest Phar
from the [Releases][github-qa-releases] page. Place the Phar in your project, or
somewhere in your [PATH][path], and make it executable. Then download the
[public key][public-key] and place it next to the executable.

Read why we release the QA Tools as a Phar [here](phar.md).

[github-qa-releases]: https://github.com/ibuildingsnl/qa-tools-v3/releases
[path]: https://en.wikipedia.org/wiki/PATH_(variable)
[public-key]: build/release/qa-tools.phar.pubkey

## Upgrading

The QA Tools Phar distributable is self-updateable in a way that is very similar
to Composer. The following command will check the QA Tools'
[Releases][qa-tools-releases] page for the latest stable version, and replace
your executable Phar:

```sh-session
$ ./qa-tools.phar self-update
```

**NB:** While Ibuildings QA Tools resides in a private repository, it needs
access to a [personal access token][personal-access-tokens] with `repo`
permissions, to be able to view the releases and download the latest Phar
distributable. Pass the GitHub token to QA Tools using the environment variable
`GITHUB_TOKEN`, like so:

```sh-session
$ GITHUB_TOKEN=xxx ./qa-tools.phar self-update
```

... or by storing it in a file for reuse:

```sh-session
$ GITHUB_TOKEN=`cat github-token` ./qa-tools.phar self-update
```

[personal-access-tokens]: https://github.com/settings/tokens

## Documentation

 * [Contributing guidelines](CONTRIBUTING.md)
 * [Development](docs/development.md)
    * [Configuration process](docs/development/configuration-process.md)
    * [Task development](docs/development/task-development.md)
    * [Tool development](docs/development/tool-development.md)
    * [Writing system tests](docs/development/writing-system-tests.md)
 * [Phar](docs/phar.md)
 * [Release process](docs/release-process.md)
 * [Reporting a bug](docs/reporting-a-bug.md)
 * [Ubiquitous language](docs/ubiquitous-language.md)
