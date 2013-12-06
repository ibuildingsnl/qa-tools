qa-tools
======

Home of the Ibuildings QA Tools

# What is it?
A set of QA tools with an installer script that sets up the build configuration for you.

It is meant to provide you with a decent base build setup, that conforms to the Ibuildings standards.
It is not meant to provide a solution for every use case. If you want a more complex setup, you can use the
output of this script as a base and configure it manually.

# Included tools
## Inspections
 - PHP Lint
 - PHP Copy/Paste Detector (1.4.*)
 - PHP Codesniffer (1.4.*)
 - PHP Mess Detector (1.5.*)
 - JSHint (1.0.0)

## Testing
 - PHPUnit (3.7.*)

## Other
 - Sensiolabs Security Checker (1.2-dev)
 - Git Pre-Commit Hook
 - Behat Framework setup

# Requirements
To run the tools, you need to have at leaste Apache Ant 1.7.1 installed.
To use the pre-commit hook you need to have:
    - at least git 1.7.8 installed
    - md5 installed (note that this can also be named md5sum depending on your OS)
If you want to run JSHint, you also need Node.js installed.

# Installation
It can be installed with Composer.

Add the following to your composer.json
```json
    "require-dev": {
        "ibuildings/qa-tools": "~1.0"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "***REMOVED***"
        }
    ],
```

And run `composer install --dev`

Why `require-dev`? Because you only want the QA tools on your dev environment.
On test and production environments, you should run composer install without --dev so that the qa-tools don't get installed.

# Usage
After installation, you can run `$ vendor/bin/qa-tools install`. This script will ask you which tools you want to enable and writes the build.xml file that can be used for Jenkins.
In Jenkins, use the QA-Tools template for you project, or base your project on it manually.
> If you want more options or some config that this script doesn't provide, you can simply edit the generated build.xml and config files to suit your needs. An example would be when your sources are in many different directories or when you need to exclude specific files for specific tools

## Behat
You can run your Behat features with `$ ant behat`.

### Phantomjs
Start using phantomjs web driver using: `$ phantomjs --webdriver=4444`

# Notes
Important note: the pre-commit runs on the source you actually staged for commit with `$ git add` everything else is ignored. This is to make sure that succesfull or failing builds reflect what is actually being committed.

# Contributing
If something is broken or you have a feature request, please create an issue here on the github repo. 
Better yet, create a feature branch, fix it and create a pull request! Please **do not** push directly to master or to a release branch.
