# Ibuildings QA Tools v3 [![Build Status](https://travis-ci.com/ibuildingsnl/qa-tools-v3.svg?token=JEaBsbhAuRqMRnCxyjuy&branch=master)](https://travis-ci.com/ibuildingsnl/qa-tools-v3)

A set of quality assurance tools that are easily configurable through an installer.

The QA Tools are meant to provide you with a decent base build setup, conforming to Ibuildings standards. 
They are not meant to provide a solution for every use case. If you want a more complex setup,
you can use the resulting configurations as a base and configure it manually.

The official, full documentation can be found on our [GitHub pages][gh-pages].

[gh-pages]: https://ibuildingsnl.github.io/qa-tools-v3

## Requirements

At this moment, QA Tools requires your project to be under Git version control.
Furthermore, it requires you to have a Linuxy environment with the Dash shell
(`sh`), Ant (`ant`), and the common tools `find`, `tr`, and `xargs` in your
[PATH][path]. After installing Ant, QA Tools should work on your Linux or
macOS machine.

## Installation

The recommended way to install the QA Tools is by downloading the latest Phar
from the [Releases][github-qa-releases] page. Place the Phar in your project, or
somewhere in your [PATH][path], and make it executable. Then download the
[public key][public-key] and place it next to the executable.

Read why we release the QA Tools as a Phar [here](docs/phar.md).

[github-qa-releases]: https://github.com/ibuildingsnl/qa-tools-v3/releases
[path]: https://en.wikipedia.org/wiki/PATH_(variable)
[public-key]: build/release/qa-tools.phar.pubkey

## Usage

```sh-session
Usage:
  command [options] [arguments]

Available commands:
  configure             Configure the Ibuildings QA Tools
  self-update           Updates Ibuildings QA Tools to the latest version
  help                  Displays help for a command
  list                  Lists commands
```

The `configure` subcommand will start an interactive questionnaire to help you
quickly configure various QA tools to your project's testing needs. It remembers
your answers, so you can easily reconfigure the tools.

![The configure command](docs/configure.png)

## Upgrading

The QA Tools Phar distributable is self-updateable in a way that is very similar
to Composer. The following command will check the QA Tools'
[Releases][github-qa-releases] page for the latest stable version, and replace
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

## Contributing

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
