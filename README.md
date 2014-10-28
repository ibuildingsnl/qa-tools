[![Build Status](https://travis-ci.org/ibuildingsnl/qa-tools.svg?branch=master)](https://travis-ci.org/ibuildingsnl/qa-tools)

The Ibuildings QA Tools
=======================

Home of the Ibuildings QA Tools

# What is it?
A set of QA tools with an installer script that sets up the build configuration for you.

It is meant to provide you with a decent base build setup, that conforms to the Ibuildings standards.
It is not meant to provide a solution for every use case. If you want a more complex setup, you can use the
output of this script as a base and configure it manually.

# Included tools
## Inspections
 - PHP Lint
 - PHP Copy/Paste Detector (2.*)
 - PHP Codesniffer (1.*)
 - PHP Mess Detector (2.*)
 - JSHint (2.4.*)

## Testing
 - PHPUnit (4.*)
 - Behat Framework setup

## Other
 - Sensiolabs Security Checker (1.*)
 - Git Pre-Commit Hook

# Requirements
To run the tools, you need to have at least Apache Ant 1.7.1 installed.
To use the pre-commit hook you need to have:
    - at least git 1.7.8 installed
    - md5 installed (note that this can also be named md5sum depending on your OS)
If you want to run JSHint, you also need Node.js and NPM installed. If you want JSHint to be automatically updated
please add the following to your composer.json:
```json
    "scripts": {
        "post-install-cmd": [
            "vendor/ibuildings/qa-tools/bin/qa-tools install:jshint"
        ],
        "post-update-cmd": [
            "vendor/ibuildings/qa-tools/bin/qa-tools install:jshint"
        ]
    }
```

# Installation
The QA tools can easily be installed by using composer:

```bash
composer require --dev ibuildings/qa-tools=~1.1
```

Why `--dev`? Because you only want the QA tools on your dev environment.
On test and production environments, you should run composer install with --no-dev so that the qa-tools don't get installed.

# Usage
After installation, you can run `$ vendor/bin/qa-tools install`. This script will ask you which tools you want to enable and writes a few files:
- the build.xml file that can be used with Ant on your CI server
- the build-pre-commit.xml that is used with Ant for the Git pre-commit hook
- qa-tools.json, this files contains all settings.

> If you want more options or some config that this script doesn't provide, you can simply edit the generated build.xml and config files to suit your needs.

Locally on your development environment, you can run the QA-Tools by running `$ vendor/bin/qa-tools run`. See the help on that command for more options

## Behat
You can run your Behat features with `$ vendor/bin/qa-tools run behat`.

## Phantomjs
Start using phantomjs web driver using: `$ phantomjs --webdriver=4444`

# Generated config
When running the QA Tools a qa-tools.json settings file is generated for you. All answered questions will be saved here. Next time you execute the QA Tools, the default values are the ones you answered before.
This also makes it possible to create different distributions of the file based on much used functionality for your projects.

This can be helpful if you know all new projects have to work with the same set of QA tools by adding the qa-tools.json file to the project. If you subsequently run the QA Tools installer in non-interactive mode, all settings form the qa-tools.json settings file will be used.

# Notes
Important note: the pre-commit hook runs on the source you actually staged for commit with `$ git add`, untracked file are ignored. This is to make sure that successful or failing builds reflect what is actually being committed.

# Contributing
If something is broken or you have a feature request, please create an issue here on the github repo.
Better yet, create a feature branch, fix it and create a pull request! Please **do not** push directly to master or to a release branch.
