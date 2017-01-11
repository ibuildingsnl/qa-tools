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

The recommended way to install the QA Tools is by using our installer:

```
php -r "copy('https://raw.githubusercontent.com/ibuildingsnl/qa-tools-v3/master/installer.php', 'qa-tools-setup.php');"
php -r "if (hash_file('SHA384', 'qa-tools-setup.php') === 'f150f88f78a055ebbbbdb40be10c4089276416321c10b7dc33af7714812ab91de70ef1e975a6787b7669ade95f19fdcf') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('qa-tools-setup.php'); } echo PHP_EOL;"
php qa-tools-setup.php
php -r "unlink('qa-tools-setup.php');"
```

If you want, you can use the `--install-dir` option for `qa-tool-setup.php` to indicate where QA tools
should be installed. E.g., `php qa-tools-setup.php --install-dir=/usr/local/bin`. It is recommended you
download QA tools to either your project directory or to some location that is in your [PATH][path].

To see all the options of the installer, run `php qa-tools-setup.php --help`.

Note that downloading the `qa-tools-setup.php` will not work as long as the repository is private. 
The comment below on using a `GITHUB_TOKEN` is required for the installer during the development phase
as well (i.e., while the repository is private).

Read why we release the QA Tools as a Phar [here](docs/phar.md).

[path]: https://en.wikipedia.org/wiki/PATH_(variable)

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
