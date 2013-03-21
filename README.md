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
        "ibuildings/qa-tools": "1.0.x-dev"
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
In Jenkins, use the QA-Tools template for you project, or base your project on it manually.
> If you want more options or some config that this script doesn't provide, you can simply edit the generated build.xml and config files to suit your needs. An example would be when your sources are in many different directories or when you need to exclude specific files for specific tools

