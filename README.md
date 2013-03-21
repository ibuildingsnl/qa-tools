qa-php
======

Home of the Ibuildings QA Tools

# What is it?
A set of QA tools with an installer script that sets up the build configuration for you.

# Included tools
## Inspections
 - PHP Lint
 - PHP Copy/Paste Detector (1.4.0)
 - PHP Codesniffer (1.4.4)
 - PHP Mess Detector (1.4.1)
 - JSHint (1.0.0)

## Testing
 - PHPUnit (3.7.17)

## Other
 - PHP CS Fixer (0.2)
 - Sensiolabs Security Checker (1.2-dev)
 - Git Pre-Commit Hook

# Requirements
To run the tools, you need to have Apache Ant installed on your system.
If you want to run JSHint, you also need Node.js installed.

# Installation
It can be installed with Composer.

Add the following to your composer.json
```json
    "require": {
        "ibuildings/qa-php": "1.0.x-dev"
    },
    "repositories": [
        {
            "type": "composer",
            "url": "***REMOVED***"
        }
    ],
```

And run `composer install`

# Usage
After installation, you can run `vendor/bin/console install`. This script will ask you which tools you want to enable and writes the build.xml file that can be used for Jenkins.
If you want more options or some config that this script doesn't provide, you can simply edit the generated build.xml and config files to suit your needs.
In Jenkins, use the QA-Tools template for you project, or base your project on it manually.
