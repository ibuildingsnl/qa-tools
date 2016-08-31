# Ibuildings QA Tools v3 [![Build Status](https://travis-ci.com/ibuildingsnl/qa-tools-v3.svg?token=JEaBsbhAuRqMRnCxyjuy&branch=master)](https://travis-ci.com/ibuildingsnl/qa-tools-v3)
A set of quality assurance tools that are easily configurable through an installer.

The QA Tools are meant to provide you with a decent base build setup, conforming to Ibuildings standards. 
They are not meant to provide a solution for every use case. If you want a more complex setup,
you can use the resulting configurations as a base and configure it manually.

## Installation

The recommended way to install the QA Tools is by downloading the latest Phar
from the [Releases][github-qa-releases] page. Place the Phar in your project, or
somewhere in your [PATH][path], and make it executable. If you wish, you can
remove the `.phar` extension on Unix-y systems.

Read why we release the QA Tools as a Phar [here](phar.md).

[github-qa-releases]: https://github.com/ibuildingsnl/qa-tools-v3/releases
[path]: https://en.wikipedia.org/wiki/PATH_(variable)

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

## Wanna-haves

 * [Write a nice file contents matcher](https://github.com/ibuildingsnl/qa-tools-v3/blob/061e357c07d24e4ad217fffa545015f8e79cfbac/tests/unit/Tool/PhpMd/Configurator/PhpMdConfiguratorTest.php#L74-L80)
 * Verify whether [this PHPMD rule exclusion](https://github.com/ibuildingsnl/qa-tools-v3/blob/061e357c07d24e4ad217fffa545015f8e79cfbac/src/Tool/PhpMd/Resources/templates/phpmd-default.xml.twig#L20-L23) is still needed
 * Rename `TaskDirectory` to `ToDoList`
 * Create more attractive task execution output styling; task execution currently result in yellow, unstyled and unstructured text.
 * Support verbose messages about which tools are being configured with which configurators, in which tasks they result, etc.
 * Create a nice installer script to automatically download the latest release from GitHub and verify its integrity.
